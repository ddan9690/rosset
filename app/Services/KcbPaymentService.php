<?php

namespace App\Services;

use App\Models\SolidarityFund;
use App\Models\Transaction;
use App\Models\TransactionLedger;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KcbPaymentService
{
    protected string $baseUrl;
    protected string $tokenUrl;
    protected string $consumerKey;
    protected string $consumerSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.kcb.base_url', 'https://uat.buni.kcbgroup.com');
        $this->tokenUrl = config('services.kcb.token_url', 'https://accounts.buni.kcbgroup.com/oauth2/token');
        $this->consumerKey = config('services.kcb.consumer_key');
        $this->consumerSecret = config('services.kcb.consumer_secret');
    }

    public function generateToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->asForm()
                ->post($this->tokenUrl, [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('KCB Token Generation Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('KCB Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function stkPush(string $phone, float $amount, string $accountIdentifier, string $description = 'ROSSET-SWA Payment', ?int $userId = null, string $transactionType = 'registration_fee')
    {
        $token = $this->generateToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Failed to generate KCB authentication token.'];
        }

        $normalizedPhone = preg_replace('/^\+/', '', trim($phone));
        if (str_starts_with($normalizedPhone, '0')) {
            $normalizedPhone = '254' . substr($normalizedPhone, 1);
        } elseif (str_starts_with($normalizedPhone, '7') || str_starts_with($normalizedPhone, '1')) {
            $normalizedPhone = '254' . $normalizedPhone;
        }

        try {
            $isProduction = config('app.env') === 'production';

            $response = Http::withToken($token)
                ->timeout(30)
                ->post("{$this->baseUrl}/mm/api/request/1.0.0/stkpush", [
                    'phoneNumber' => $normalizedPhone,
                    'amount' => $amount,
                    'invoiceNumber' => $accountIdentifier,
                    'sharedShortCode' => $isProduction ? false : true,
                    'orgShortCode' => '',
                    'orgPassKey' => '',
                    'callbackUrl' => config('services.kcb.callback_url'),
                    'transactionDescription' => $description,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                $checkoutRequestId = $responseData['Body']['stkCallback']['CheckoutRequestID']
                    ?? $responseData['CheckoutRequestID']
                    ?? $responseData['checkoutRequestID']
                    ?? null;

                if ($checkoutRequestId && $userId) {
                    Cache::put("kcb_user_{$checkoutRequestId}", $userId, now()->addMinutes(15));
                    Cache::put("kcb_type_{$checkoutRequestId}", $transactionType, now()->addMinutes(15));

                    // Also create a initial pending transaction record for absolute reliability
                    Transaction::create([
                        'user_id' => $userId,
                        'reference_number' => 'PENDING-' . $checkoutRequestId,
                        'checkout_request_id' => $checkoutRequestId,
                        'type' => $transactionType,
                        'amount' => $amount,
                        'currency' => 'KES',
                        'status' => 'pending',
                        'phone_number' => $normalizedPhone,
                        'description' => $description,
                    ]);
                }

                return ['success' => true, 'data' => $responseData];
            }

            Log::error('KCB STK Push Failed: ' . $response->body());
            return ['success' => false, 'message' => 'KCB rejected the STK Push request.'];
        } catch (\Exception $e) {
            Log::error('KCB STK Push Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function processIpnNotification(array $data)
    {
        try {
            $stkCallback = $data['Body']['stkCallback'] ?? $data;
            $resultCode = $stkCallback['ResultCode'] ?? $stkCallback['resultCode'] ?? 1;
            $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? $stkCallback['checkoutRequestID'] ?? null;
            $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? $stkCallback['callbackMetadata'] ?? [];

            $amount = 0;
            $mpesaReceiptNumber = null;
            $phoneNumber = null;

            foreach ($callbackMetadata as $item) {
                $name = $item['Name'] ?? $item['name'] ?? '';
                $value = $item['Value'] ?? $item['value'] ?? '';

                if ($name === 'Amount') $amount = $value;
                if ($name === 'MpesaReceiptNumber') $mpesaReceiptNumber = $value;
                if ($name === 'PhoneNumber') $phoneNumber = $value;
            }

            // 1. Resolve user ID using Cache or fallback to the pending transaction record by checkout_request_id
            $userId = Cache::get("kcb_user_{$checkoutRequestId}");
            $transactionType = Cache::get("kcb_type_{$checkoutRequestId}");

            if (!$userId || !$transactionType) {
                $pendingTx = Transaction::where('checkout_request_id', $checkoutRequestId)->first();
                if ($pendingTx) {
                    $userId = $pendingTx->user_id;
                    $transactionType = $pendingTx->type;
                }
            }

            $user = $userId ? User::find($userId) : User::latest()->first();
            $resolvedUserId = $user ? $user->id : 1;
            $resolvedType = $transactionType ?? 'wallet_topup';

            if ($resultCode == 0 || $resultCode === '0') {
                $reference = $mpesaReceiptNumber ?? 'KCB-' . uniqid();

                // 2. Save entry to transaction_ledgers table
                TransactionLedger::create([
                    'user_id' => $resolvedUserId,
                    'reference' => $reference,
                    'amount' => $amount,
                    'type' => 'credit',
                    'channel' => 'KCB Paybill / STK IPN',
                    'account_identifier' => $checkoutRequestId ?? '7936435',
                    'phone_number' => $phoneNumber,
                    'description' => $resolvedType === 'wallet_topup' ? 'Solidarity Wallet Top-up' : 'Registration Fee Payment',
                    'status' => 'Completed',
                    'raw_payload' => $data,
                ]);

                // 3. Update or create entry in transactions table
                $existingTx = Transaction::where('checkout_request_id', $checkoutRequestId)->first();
                if ($existingTx) {
                    $existingTx->update([
                        'reference_number' => $reference,
                        'status' => 'success',
                        'amount' => $amount > 0 ? $amount : $existingTx->amount,
                        'gateway_response' => json_encode($data),
                        'paid_at' => now(),
                    ]);
                } else {
                    Transaction::create([
                        'user_id' => $resolvedUserId,
                        'reference_number' => $reference,
                        'checkout_request_id' => $checkoutRequestId,
                        'type' => $resolvedType,
                        'amount' => $amount,
                        'currency' => 'KES',
                        'status' => 'success', 
                        'phone_number' => $phoneNumber,
                        'description' => 'M-Pesa Payment via KCB STK',
                        'gateway_response' => json_encode($data),
                        'paid_at' => now(),
                    ]);
                }

                // 4. Update Solidarity Fund or User Registration Status
                if ($resolvedType === 'wallet_topup') {
                    $wallet = SolidarityFund::firstOrCreate(
                        ['user_id' => $resolvedUserId],
                        ['balance' => 0, 'total_topups' => 0, 'total_deductions' => 0]
                    );
                    $wallet->increment('balance', $amount);
                    $wallet->increment('total_topups', $amount);
                    Log::info("Solidarity wallet successfully credited with KES {$amount} for Member ID: {$resolvedUserId}");
                } else {
                    if ($user) {
                        $user->update([
                            'registration_fee_paid' => true,
                            'status' => 'active',
                            'is_profile_complete' => true,
                        ]);
                    }
                }

                return ['ResultCode' => 0, 'ResultDesc' => 'Success'];
            }

            // Handle failed payment result code
            Transaction::where('checkout_request_id', $checkoutRequestId)->update(['status' => 'failed']);
            Log::warning("KCB STK Callback returned non-success ResultCode: {$resultCode}");
            return ['ResultCode' => 1, 'ResultDesc' => 'Failed transaction result code'];
        } catch (\Exception $e) {
            Log::error('IPN Processing Error: ' . $e->getMessage());
            return ['ResultCode' => 1, 'ResultDesc' => $e->getMessage()];
        }
    }
}