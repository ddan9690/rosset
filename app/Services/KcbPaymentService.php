<?php

namespace App\Services;

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

            Log::info('KCB Token Response', [
                'status' => $response->status(),
                'body' => $response->json(),
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
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return null;
        }
    }

    /**
     * Send KCB STK Push.
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
            $token = $this->generateToken();

            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Unable to authenticate with KCB.',
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

            Log::info('KCB STK Push Response', [
                'status' => $response->status(),
                'body' => $responseData,
            ]);

            if (!$response->successful()) {
                Log::error('KCB STK Push HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'KCB STK request failed: ' . data_get($responseData, 'header.statusDescription', 'Invalid Request'),
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
                    'invoice_number' => $invoiceNumber,
                ]);

                return [
                    'success' => false,
                    'message' => 'KCB did not return a CheckoutRequestID.',
                    'response' => $responseData,
                ];
            }

            Cache::put(
                'kcb_payment_' . $checkoutRequestId,
                [
                    'user_id' => $userId,
                    'type' => $transactionType,
                    'case_number' => $caseNumber,
                    'phone_number' => $phone,
                    'amount' => $amount,
                    'invoice_number' => $invoiceNumber,
                    'merchant_request_id' => $merchantRequestId,
                ],
                now()->addHours(24)
            );

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
                'message' => 'STK Push sent successfully.',
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
                'invoice_number' => $invoiceNumber,
                'response' => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error('KCB STK Push Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while sending the STK Push.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process KCB IPN callback.
     */
    public function processIpnNotification(array $data): array
    {
        Log::info('KCB IPN Processing Started', [
            'payload' => $data,
        ]);

        try {
            $stkCallback =
                data_get($data, 'Body.stkCallback')
                ?? data_get($data, 'body.stkCallback')
                ?? data_get($data, 'stkCallback')
                ?? $data;

            if (!is_array($stkCallback)) {
                Log::error('KCB IPN Invalid Callback Structure', [
                    'payload' => $data,
                ]);

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
                ?? data_get($stkCallback, 'checkoutRequestID')
                ?? data_get($stkCallback, 'CheckoutRequestId');

            $merchantRequestId =
                data_get($stkCallback, 'MerchantRequestID')
                ?? data_get($stkCallback, 'merchantRequestID')
                ?? data_get($stkCallback, 'MerchantRequestId');

            if (!$checkoutRequestId) {
                Log::error('KCB IPN Missing Checkout Request ID', [
                    'payload' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => 'CheckoutRequestID missing from KCB callback.',
                ];
            }

            $items = data_get($stkCallback, 'CallbackMetadata.Item')
                ?? data_get($stkCallback, 'callbackMetadata.Item')
                ?? [];

            if (!is_array($items)) {
                $items = [];
            }

            $metadata = [];
            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $name = $item['Name'] ?? null;
                if ($name) {
                    $metadata[$name] = $item['Value'] ?? null;
                }
            }

            $amount = $metadata['Amount'] ?? null;
            $receiptNumber = $metadata['MpesaReceiptNumber']
                ?? $metadata['M-PesaReceiptNumber']
                ?? $metadata['ReceiptNumber']
                ?? null;

            $transactionDate = $metadata['TransactionDate'] ?? null;
            $phoneNumber = $metadata['PhoneNumber'] ?? null;

            $transaction = Transaction::where(
                'checkout_request_id',
                $checkoutRequestId
            )->first();

            $cachedData = Cache::get('kcb_payment_' . $checkoutRequestId);
            $userId = $cachedData['user_id'] ?? null;
            $transactionType = $cachedData['type'] ?? null;
            $caseNumber = $cachedData['case_number'] ?? null;

            if (!$merchantRequestId) {
                $merchantRequestId = $cachedData['merchant_request_id'] ?? null;
            }

            if ($transaction) {
                $userId = $userId ?? $transaction->user_id;
                $transactionType = $transactionType ?? $transaction->type;
                $caseNumber = $caseNumber ?? $transaction->case_number;
            }

            if (!$transaction || !$userId) {
                Log::error('KCB IPN Unable To Resolve Transaction/User', [
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                    'transaction_exists' => (bool) $transaction,
                    'user_id' => $userId,
                    'payload' => $data,
                ]);

                if ($transaction) {
                    $transaction->update([
                        'status' => 'failed',
                        'gateway_response' => $data,
                    ]);
                }

                return [
                    'success' => false,
                    'message' => 'Unable to resolve KCB transaction.',
                ];
            }

            if ((int) $resultCode === 0) {
                DB::transaction(function () use (
                    $transaction,
                    $data,
                    $userId,
                    $transactionType,
                    $caseNumber,
                    $amount,
                    $receiptNumber,
                    $phoneNumber,
                    $checkoutRequestId,
                    $merchantRequestId
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
                        Log::info('KCB IPN Already Processed', [
                            'transaction_id' => $lockedTransaction->id,
                            'checkout_request_id' => $checkoutRequestId,
                            'merchant_request_id' => $merchantRequestId,
                        ]);

                        if (empty($lockedTransaction->gateway_response)) {
                            $lockedTransaction->update([
                                'gateway_response' => $data,
                            ]);
                        }

                        return;
                    }

                    $finalAmount = $amount !== null
                        ? (float) $amount
                        : (float) $lockedTransaction->amount;

                    $finalPhone = $phoneNumber ?: $lockedTransaction->phone_number;
                    $ledgerReference = $receiptNumber ?: 'KCB-' . $checkoutRequestId;

                    TransactionLedger::firstOrCreate(
                        [
                            'reference' => $ledgerReference,
                        ],
                        [
                            'user_id' => $userId,
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

                    if ($transactionType === 'registration_fee') {
                        $user = User::find($userId);

                        if ($user) {
                            $user->update([
                                'registration_fee_paid' => true,
                                'status' => 'active',
                            ]);
                        }

                        Log::info('KCB Registration Fee Updated', [
                            'user_id' => $userId,
                            'transaction_id' => $lockedTransaction->id,
                        ]);
                    } elseif ($transactionType === 'wallet_topup') {
                        $user = User::find($userId);

                        if ($user) {
                            $wallet = method_exists($user, 'solidarityFund')
                                ? $user->solidarityFund()->first()
                                : null;

                            if ($wallet) {
                                $wallet->increment('balance', $finalAmount);
                                $wallet->increment('total_topups', $finalAmount);
                            } elseif (method_exists($user, 'solidarityFund')) {
                                $user->solidarityFund()->create([
                                    'balance' => $finalAmount,
                                    'total_topups' => $finalAmount,
                                    'total_deductions' => 0,
                                ]);
                            }
                        }

                        Log::info('KCB Wallet Topup Processed', [
                            'user_id' => $userId,
                            'amount' => $finalAmount,
                            'transaction_id' => $lockedTransaction->id,
                        ]);
                    } elseif ($transactionType === 'benevolence_contribution') {
                        Log::info('KCB Benevolence Contribution Processed', [
                            'user_id' => $userId,
                            'case_number' => $caseNumber,
                            'amount' => $finalAmount,
                            'transaction_id' => $lockedTransaction->id,
                        ]);
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

                Log::warning('KCB Payment Failed Callback', [
                    'checkout_request_id' => $checkoutRequestId,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                ]);

                return [
                    'success' => false,
                    'message' => $resultDesc,
                ];
            }
        } catch (\Throwable $e) {
            Log::error('KCB IPN Processing Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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

    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . strtoupper(Str::random(10));
    }
}