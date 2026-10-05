<?php

namespace App\Services;

use App\Models\SolidarityFund;
use App\Models\Transaction;
use App\Models\TransactionLedger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KcbPaymentService
{
    protected ?string $baseUrl;
    protected ?string $tokenUrl;
    protected ?string $consumerKey;
    protected ?string $consumerSecret;
    protected ?string $accountNumber;
    protected ?string $callbackUrl;
    protected ?string $paybillNumber;

    public function __construct()
    {
        $isProduction = config('app.env') === 'production';

        $this->consumerKey = $isProduction 
            ? config('services.kcb.prod_consumer_key', env('KCB_PROD_CONSUMER_KEY', ''))
            : config('services.kcb.sandbox_consumer_key', env('KCB_SANDBOX_CONSUMER_KEY', ''));

        $this->consumerSecret = $isProduction 
            ? config('services.kcb.prod_consumer_secret', env('KCB_PROD_CONSUMER_SECRET', ''))
            : config('services.kcb.sandbox_consumer_secret', env('KCB_SANDBOX_CONSUMER_SECRET', ''));

        $this->baseUrl = rtrim($isProduction 
            ? config('services.kcb.prod_base_url', env('KCB_PROD_BASE_URL', 'https://api.kcbgroup.com'))
            : config('services.kcb.sandbox_base_url', env('KCB_SANDBOX_BASE_URL', 'https://uat.buni.kcbgroup.com')), '/');

        $this->tokenUrl = $isProduction 
            ? config('services.kcb.prod_token_url', env('KCB_PROD_TOKEN_URL', 'https://accounts.kcbgroup.com/oauth2/token'))
            : config('services.kcb.sandbox_token_url', env('KCB_SANDBOX_TOKEN_URL', 'https://accounts.buni.kcbgroup.com/oauth2/token'));

        $this->callbackUrl = $isProduction 
            ? config('services.kcb.prod_callback_url', env('KCB_PROD_CALLBACK_URL', 'https://rosset.co.ke/kcb/ipn'))
            : config('services.kcb.sandbox_callback_url', env('KCB_SANDBOX_CALLBACK_URL', ''));

        $this->accountNumber = config('services.kcb.account_number', env('KCB_ACCOUNT_NUMBER', '7936435'));
        $this->paybillNumber = config('services.kcb.paybill_number', env('KCB_PAYBILL_NUMBER', ''));
    }

    /**
     * Generate KCB OAuth access token.
     */
    public function generateToken(): ?string
    {
        try {
            Log::info('KCB Token Request Started');

            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
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

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error('KCB Token Generation Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Send KCB STK Push request to the gateway api.
     */
    public function stkPush(
        string $phone,
        float $amount,
        ?string $accountIdentifier = null,
        string $description = 'Payment for transaction',
        ?int $userId = null,
        string $transactionType = 'payment'
    ): array {
        try {
            $token = $this->generateToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Unable to authenticate with payment gateway.',
                ];
            }

            $phone = $this->normalizePhoneNumber($phone);
            $invoiceNumber = $accountIdentifier ?: ($this->accountNumber ?: '7936435');

            $payload = [
                'phoneNumber' => $phone,
                'amount' => $amount,
                'invoiceNumber' => $invoiceNumber,
                'sharedShortCode' => true,
                'orgShortCode' => $this->paybillNumber ?: '',
                'orgPassKey' => '',
                'callbackUrl' => $this->callbackUrl,
                'transactionDescription' => $description,
            ];

            $response = Http::withToken($token)
                ->acceptJson()
                ->contentType('application/json')
                ->timeout(60)
                ->post($this->baseUrl . '/mm/api/request/1.0.0/stkpush', $payload);

            $responseData = $response->json();

            if (!$response->successful()) {
                Log::error('KCB STK Push HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
                return [
                    'success' => false,
                    'message' => 'Payment request failed at gateway.',
                    'response' => $responseData,
                ];
            }

            $checkoutRequestId = data_get($responseData, 'response.CheckoutRequestID')
                ?? data_get($responseData, 'Response.CheckoutRequestID')
                ?? data_get($responseData, 'CheckoutRequestID')
                 ?? data_get($responseData, 'checkoutRequestID');

            $merchantRequestId = data_get($responseData, 'response.MerchantRequestID')
                ?? data_get($responseData, 'Response.MerchantRequestID')
                ?? data_get($responseData, 'MerchantRequestID')
                ?? data_get($responseData, 'merchantRequestID');

            if (!$checkoutRequestId) {
                Log::error('KCB STK Push Missing CheckoutRequestID', ['response' => $responseData]);
                return [
                    'success' => false,
                    'message' => 'Gateway response missing CheckoutRequestID.',
                    'response' => $responseData,
                ];
            }

            // Automatically record pending transaction if user ID is supplied
            if ($userId) {
                Transaction::create([
                    'user_id' => $userId,
                    'reference_number' => null,
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId,
                    'type' => $transactionType,
                    'amount' => $amount,
                    'currency' => 'KES',
                    'status' => 'pending',
                    'phone_number' => $phone,
                    'description' => $description,
                ]);
            }

            return [
                'success' => true,
                'message' => 'STK push sent successfully.',
                'checkout_request_id' => $checkoutRequestId,
                'merchant_request_id' => $merchantRequestId,
                'phone_number' => $phone,
                'invoice_number' => $invoiceNumber,
                'response' => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error('KCB STK Push Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Payment service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process incoming IPN notification payloads.
     */
    public function processIpnNotification(array $payload): array
    {
        Log::info('KCB IPN Processing Started', ['payload' => $payload]);

        $stkCallback = data_get($payload, 'Body.stkCallback')
            ?? data_get($payload, 'body.stkCallback')
            ?? data_get($payload, 'stkCallback')
            ?? $payload;

        if (!is_array($stkCallback)) {
            return [
                'success' => false,
                'message' => 'Invalid KCB callback structure.',
            ];
        }

        $resultCode = data_get($stkCallback, 'ResultCode');
        $resultDesc = data_get($stkCallback, 'ResultDesc', 'KCB payment callback received.');
        $checkoutRequestId = data_get($stkCallback, 'CheckoutRequestID') ?? data_get($stkCallback, 'checkoutRequestID');

        if (!$checkoutRequestId) {
            Log::error('KCB IPN CheckoutRequestID Missing', ['payload' => $payload]);
            return [
                'success' => false,
                'message' => 'CheckoutRequestID missing from KCB callback.',
            ];
        }

        $transaction = Transaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            Log::error('KCB IPN Transaction Not Found', ['checkout_request_id' => $checkoutRequestId]);
            return [
                'success' => false,
                'message' => 'Unable to resolve KCB transaction.',
            ];
        }

        $items = data_get($stkCallback, 'CallbackMetadata.Item') ?? data_get($stkCallback, 'callbackMetadata.Item') ?? [];
        $metadata = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['Name'])) {
                $metadata[$item['Name']] = $item['Value'] ?? null;
            }
        }

        $amount = $metadata['Amount'] ?? $transaction->amount;
        $receiptNumber = $metadata['MpesaReceiptNumber'] ?? $metadata['M-PesaReceiptNumber'] ?? $metadata['ReceiptNumber'] ?? null;
        $phoneNumber = $metadata['PhoneNumber'] ?? $transaction->phone_number;

        if ((int) $resultCode === 0) {
            DB::transaction(function () use ($transaction, $payload, $amount, $receiptNumber, $phoneNumber, $checkoutRequestId) {
                $lockedTransaction = Transaction::where('id', $transaction->id)->lockForUpdate()->first();

                if (!$lockedTransaction || $lockedTransaction->status === 'success') {
                    return;
                }

                $finalAmount = (int) $amount;
                $finalPhone = $phoneNumber ?: $lockedTransaction->phone_number;
                $ledgerReference = $receiptNumber ?: 'KCB-' . $checkoutRequestId;
                $accountNumber = $this->accountNumber ?: '7936435';

                TransactionLedger::firstOrCreate(
                    ['reference' => $ledgerReference],
                    [
                        'user_id' => $lockedTransaction->user_id,
                        'amount' => $finalAmount,
                        'type' => 'credit',
                        'channel' => 'KCB Paybill / STK IPN',
                        'account_identifier' => $accountNumber,
                        'phone_number' => $finalPhone,
                        'description' => $lockedTransaction->description ?: 'KCB Payment',
                        'status' => 'Completed',
                        'raw_payload' => $payload,
                    ]
                );

                $lockedTransaction->update([
                    'status' => 'success',
                    'reference_number' => $receiptNumber ?: $lockedTransaction->reference_number,
                    'amount' => $finalAmount,
                    'phone_number' => $finalPhone,
                    'gateway_response' => $payload,
                    'paid_at' => now(),
                ]);

                // If this was a registration fee, activate the user immediately on success
                if ($lockedTransaction->type === 'registration_fee' && $lockedTransaction->user) {
                    $lockedTransaction->user->activateAfterPayment();
                }

                // If this was a wallet top-up, increment the SolidarityFund balance and total topups
                if ($lockedTransaction->type === 'wallet_topup' && $lockedTransaction->user_id) {
                    $solidarityFund = SolidarityFund::firstOrCreate(
                        ['user_id' => $lockedTransaction->user_id],
                        ['balance' => 0, 'total_topups' => 0, 'total_deductions' => 0]
                    );

                    $solidarityFund->increment('balance', $finalAmount);
                    $solidarityFund->increment('total_topups', $finalAmount);
                }

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
            });

            Cache::forget('kcb_payment_' . $checkoutRequestId);

            return [
                'success' => true,
                'message' => 'IPN processed successfully.',
            ];
        } else {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => $payload,
            ]);

            return [
                'success' => false,
                'message' => $resultDesc,
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