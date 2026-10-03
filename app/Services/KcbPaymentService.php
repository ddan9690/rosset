<?php

namespace App\Services;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\Transaction;
use App\Models\TransactionLedger;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KcbPaymentService
{
    protected string $baseUrl;
    protected string $tokenUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $accountNumber;
    protected string $callbackUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.kcb.base_url', env('KCB_BASE_URL')),
            '/'
        );

        $this->tokenUrl = config(
            'services.kcb.token_url',
            env('KCB_TOKEN_URL')
        );

        $this->consumerKey = config(
            'services.kcb.consumer_key',
            env('KCB_CONSUMER_KEY')
        );

        $this->consumerSecret = config(
            'services.kcb.consumer_secret',
            env('KCB_CONSUMER_SECRET')
        );

        $this->accountNumber = config(
            'services.kcb.account_number',
            env('KCB_ACCOUNT_NUMBER', '7936435')
        );

        $this->callbackUrl = config(
            'services.kcb.callback_url',
            env(
                'KCB_CALLBACK_URL',
                'https://a22a-154-159-237-32.ngrok-free.app/kcb/ipn'
            )
        );
    }

    /**
     * Generate KCB OAuth access token.
     */
    public function generateToken(): ?string
    {
        try {
            Log::info('KCB Token Request Started');

            $response = Http::withBasicAuth(
                $this->consumerKey,
                $this->consumerSecret
            )
                ->asForm()
                ->acceptJson()
                ->timeout(30)
                ->post($this->tokenUrl, [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$response->successful()) {
                Log::error('KCB Token Generation Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $token = $response->json('access_token');

            if (!$token) {
                Log::error('KCB Token Missing From Response', [
                    'response' => $response->json(),
                ]);

                return null;
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error('KCB Token Generation Exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Send KCB STK Push with duplicate pending guard and user-friendly messages.
     */
    public function stkPush(
        string $phone,
        float $amount,
        ?string $accountIdentifier = null,
        string $description = 'Payment for transaction',
        ?int $userId = null,
        string $transactionType = 'registration_fee',
        ?string $caseNumber = null
    ): array {
        try {
            // Guard: Prevent sending a new STK push if an active pending transaction already exists
            if ($userId && $caseNumber) {
                $existingPending = Transaction::where('user_id', $userId)
                    ->where('case_number', $caseNumber)
                    ->where('status', 'pending')
                    ->where('created_at', '>=', now()->subMinutes(2))
                    ->first();

                if ($existingPending) {
                    Log::warning('KCB STK Push Blocked: Active Pending Transaction Exists', [
                        'user_id' => $userId,
                        'case_number' => $caseNumber,
                    ]);

                    return [
                        'success' => false,
                        'message' => 'Payment not successful. Try again in a moment.',
                    ];
                }
            }

            $token = $this->generateToken();

            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Payment not successful. Try again in a moment.',
                ];
            }

            $phone = $this->normalizePhoneNumber($phone);
            $invoiceNumber = $accountIdentifier ?: $this->accountNumber;

            $payload = [
                'phoneNumber' => $phone,
                'amount' => $amount,
                'invoiceNumber' => $invoiceNumber,
                'sharedShortCode' => true,
                'orgShortCode' => '',
                'orgPassKey' => '',
                'callbackUrl' => $this->callbackUrl,
                'transactionDescription' => $description,
            ];

            Log::info('KCB STK Push Request', [
                'payload' => $payload,
                'user_id' => $userId,
                'transaction_type' => $transactionType,
                'case_number' => $caseNumber,
            ]);

            $response = Http::withToken($token)
                ->acceptJson()
                ->contentType('application/json')
                ->timeout(60)
                ->post(
                    $this->baseUrl . '/mm/api/request/1.0.0/stkpush',
                    $payload
                );

            $responseData = $response->json();

            if (!$response->successful()) {
                Log::error('KCB STK Push HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Payment not successful. Try again in a moment.',
                    'response' => $responseData,
                ];
            }

            $checkoutRequestId =
                data_get($responseData, 'response.CheckoutRequestID')
                ?? data_get($responseData, 'Response.CheckoutRequestID')
                ?? data_get($responseData, 'CheckoutRequestID')
                ?? data_get($responseData, 'checkoutRequestID');

            $merchantRequestId =
                data_get($responseData, 'response.MerchantRequestID')
                ?? data_get($responseData, 'Response.MerchantRequestID')
                ?? data_get($responseData, 'MerchantRequestID')
                ?? data_get($responseData, 'merchantRequestID');

            if (!$checkoutRequestId) {
                Log::error('KCB STK Push Missing CheckoutRequestID', [
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => 'Payment not successful. Try again in a moment.',
                    'response' => $responseData,
                ];
            }

            Transaction::create([
                'user_id' => $userId,
                'reference_number' => null,
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
                'type' => $transactionType,
                'case_number' => $caseNumber,
                'amount' => $amount,
                'currency' => 'KES',
                'status' => 'pending',
                'phone_number' => $phone,
                'description' => $description,
            ]);

            return [
                'success' => true,
                'message' => 'STK push sent! Please check your phone and enter your PIN to complete the payment.',
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
                'invoice_number' => $invoiceNumber,
                'response' => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error('KCB STK Push Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment not successful. Try again in a moment.',
            ];
        }
    }

    /**
     * Process KCB IPN callback with cleanup of older pending attempts.
     */
    public function processIpnNotification(array $data): array
    {
        Log::info('KCB IPN Processing Started', ['payload' => $data]);

        try {
            $stkCallback =
                data_get($data, 'Body.stkCallback')
                ?? data_get($data, 'body.stkCallback')
                ?? data_get($data, 'stkCallback')
                ?? $data;

            if (!is_array($stkCallback)) {
                return [
                    'success' => false,
                    'message' => 'Invalid KCB callback structure.',
                ];
            }

            $resultCode = data_get($stkCallback, 'ResultCode');
            $resultDesc = data_get(
                $stkCallback,
                'ResultDesc',
                'KCB payment callback received.'
            );

            $checkoutRequestId =
                data_get($stkCallback, 'CheckoutRequestID')
                ?? data_get($stkCallback, 'checkoutRequestID');

            if (!$checkoutRequestId) {
                return [
                    'success' => false,
                    'message' => 'CheckoutRequestID missing from KCB callback.',
                ];
            }

            $transaction = Transaction::where('checkout_request_id', $checkoutRequestId)->first();

            if (!$transaction) {
                Log::error('KCB IPN Transaction Not Found in Database', [
                    'checkout_request_id' => $checkoutRequestId,
                ]);

                return [
                    'success' => false,
                    'message' => 'Unable to resolve KCB transaction.',
                ];
            }

            $items = data_get($stkCallback, 'CallbackMetadata.Item')
                ?? data_get($stkCallback, 'callbackMetadata.Item')
                ?? [];

            $metadata = [];
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (is_array($item) && isset($item['Name'])) {
                        $metadata[$item['Name']] = $item['Value'] ?? null;
                    }
                }
            }

            $amount = $metadata['Amount'] ?? $transaction->amount;
            $receiptNumber = $metadata['MpesaReceiptNumber']
                ?? $metadata['M-PesaReceiptNumber']
                ?? $metadata['ReceiptNumber']
                ?? null;

            $phoneNumber = $metadata['PhoneNumber'] ?? $transaction->phone_number;

            if ((int) $resultCode === 0) {
                DB::transaction(function () use (
                    $transaction,
                    $data,
                    $amount,
                    $receiptNumber,
                    $phoneNumber,
                    $checkoutRequestId
                ) {
                    $lockedTransaction = Transaction::where(
                        'id',
                        $transaction->id
                    )->lockForUpdate()->first();

                    if (!$lockedTransaction) {
                        throw new \RuntimeException(
                            'Transaction disappeared during IPN processing.'
                        );
                    }

                    if ($lockedTransaction->status === 'success') {
                        return;
                    }

                    $finalAmount = (int) $amount; // Matched to full integer amount columns
                    $finalPhone = $phoneNumber ?: $lockedTransaction->phone_number;
                    $ledgerReference = $receiptNumber ?: 'KCB-' . $checkoutRequestId;

                    TransactionLedger::firstOrCreate(
                        [
                            'reference' => $ledgerReference,
                        ],
                        [
                            'user_id' => $lockedTransaction->user_id,
                            'amount' => $finalAmount,
                            'type' => 'credit',
                            'channel' => 'KCB Paybill / STK IPN',
                            'account_identifier' => $this->accountNumber,
                            'phone_number' => $finalPhone,
                            'description' => $lockedTransaction->description ?: 'KCB Payment',
                            'status' => 'Completed',
                            'raw_payload' => $data,
                        ]
                    );

                    $lockedTransaction->update([
                        'status' => 'success',
                        'reference_number' => $receiptNumber ?: $lockedTransaction->reference_number,
                        'amount' => $finalAmount,
                        'phone_number' => $finalPhone,
                        'gateway_response' => $data,
                        'paid_at' => now(),
                    ]);

                    // Automatically record the contribution if this transaction is tied to a benevolence case
                    if ($lockedTransaction->case_number && $lockedTransaction->type === 'benevolence_contribution') {
                        $benevolenceCase = BenevolenceCase::where('case_number', $lockedTransaction->case_number)->first();

                        if ($benevolenceCase) {
                            BenevolenceContribution::updateOrCreate(
                                [
                                    'transaction_id' => $lockedTransaction->id,
                                ],
                                [
                                    'benevolence_case_id' => $benevolenceCase->id,
                                    'user_id' => $lockedTransaction->user_id,
                                    'amount' => $finalAmount,
                                    'payment_channel' => 'MPESA Prompt',
                                    'reference_number' => $receiptNumber ?: ('KCB-' . $checkoutRequestId),
                                    'notes' => 'Paid via MPESA Prompt',
                                ]
                            );
                        }
                    }

                    // Clean up: Mark any other older pending transactions for this user and case as failed
                    if ($lockedTransaction->case_number) {
                        Transaction::where('user_id', $lockedTransaction->user_id)
                            ->where('case_number', $lockedTransaction->case_number)
                            ->where('id', '!=', $lockedTransaction->id)
                            ->where('status', 'pending')
                            ->update([
                                'status' => 'failed',
                                'gateway_response' => ['note' => 'Superseded by successful transaction ID ' . $lockedTransaction->id],
                            ]);
                    }

                    $userId = $lockedTransaction->user_id;
                    $transactionType = $lockedTransaction->type;

                    if ($transactionType === 'registration_fee') {
                        $user = User::find($userId);
                        if ($user && method_exists($user, 'activateAfterPayment')) {
                            $user->activateAfterPayment();
                        }
                    } elseif ($transactionType === 'wallet_topup') {
                        $user = User::find($userId);
                        if ($user && method_exists($user, 'solidarityFund')) {
                            $wallet = $user->solidarityFund()->first();
                            if ($wallet) {
                                $wallet->increment('balance', $finalAmount);
                                $wallet->increment('total_topups', $finalAmount);
                            } else {
                                $user->solidarityFund()->create([
                                    'balance' => $finalAmount,
                                    'total_topups' => $finalAmount,
                                    'total_deductions' => 0,
                                ]);
                            }
                        }
                    }
                });

                Cache::forget('kcb_payment_' . $checkoutRequestId);

                return [
                    'success' => true,
                    'message' => 'IPN processed successfully.',
                ];
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => $resultDesc,
                ];
            }
        } catch (\Throwable $e) {
            Log::error('KCB IPN Processing Exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while processing IPN.',
            ];
        }
    }

    protected function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '07') || str_starts_with($phone, '01')) {
            $phone = '254' . substr($phone, 1);
        }

        return $phone;
    }
}