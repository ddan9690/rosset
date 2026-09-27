<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Models\TransactionLedger;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessPaymentAction
{
    /**
     * Process incoming KCB STK push callback data.
     */
    public function handle(array $data)
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

            // 1. Resolve user via cached CheckoutRequestID matching the session
            $userId = Cache::get("kcb_user_{$checkoutRequestId}");
            $user = $userId ? User::find($userId) : User::latest()->first();
            $resolvedUserId = $user ? $user->id : 1;

            if ($resultCode == 0 || $resultCode === '0') {
                $reference = $mpesaReceiptNumber ?? 'KCB-' . uniqid();

                // 2. Determine transaction type (e.g., registration_fee, solidarity_fee, case_contribution)
                $transactionType = Cache::get("kcb_type_{$checkoutRequestId}") ?? 'registration_fee';

                // 3. Save entry to transaction_ledgers table (Shared uniform ledger)
                TransactionLedger::create([
                    'user_id' => $resolvedUserId,
                    'reference' => $reference,
                    'amount' => $amount,
                    'type' => 'credit',
                    'channel' => 'KCB Paybill / STK IPN',
                    'account_identifier' => $checkoutRequestId ?? '7936435',
                    'phone_number' => $phoneNumber,
                    'description' => ucfirst(str_replace('_', ' ', $transactionType)) . ' Payment',
                    'status' => 'Completed',
                    'raw_payload' => $data,
                ]);

                // 4. Save entry to transactions table (Component/Feature-specific tracking)
                Transaction::create([
                    'user_id' => $resolvedUserId,
                    'reference_number' => $reference,
                    'checkout_request_id' => $checkoutRequestId,
                    'type' => $transactionType,
                    'amount' => $amount,
                    'currency' => 'KES',
                    'status' => 'success', 
                    'phone_number' => $phoneNumber,
                    'description' => ucfirst(str_replace('_', ' ', $transactionType)) . ' via KCB STK',
                    'gateway_response' => json_encode($data),
                    'paid_at' => now(),
                ]);

                // 5. Handle component/feature-specific database state operations
                $this->handleComponentState($transactionType, $user, $amount, $data);

                // Clean up temporary cache keys
                Cache::forget("kcb_user_{$checkoutRequestId}");
                Cache::forget("kcb_type_{$checkoutRequestId}");

                return ['ResultCode' => 0, 'ResultDesc' => 'Success'];
            }

            Log::warning("KCB STK Callback returned non-success ResultCode: {$resultCode}");
            return ['ResultCode' => 1, 'ResultDesc' => 'Failed transaction result code'];

        } catch (\Exception $e) {
            Log::error('ProcessPaymentAction Error: ' . $e->getMessage());
            return ['ResultCode' => 1, 'ResultDesc' => $e->getMessage()];
        }
    }

    /**
     * Handle feature-specific post-payment database operations.
     */
    protected function handleComponentState(string $type, ?User $user, float $amount, array $payload)
    {
        if (!$user) {
            return;
        }

        switch ($type) {
            case 'registration_fee':
                $user->update([
                    'registration_fee_paid' => true,
                    'status' => 'active',
                    'is_profile_complete' => true,
                ]);
                Log::info("Registration fee state successfully updated for Member ID: {$user->id}");
                break;

            case 'solidarity_fee':
                // TODO: Handle solidarity fee specific user states or tables later
                Log::info("Solidarity fee payment recorded for Member ID: {$user->id}");
                break;

            case 'benevolence_contribution':
            case 'case_contribution':
                // TODO: Handle case contribution specific tables later
                Log::info("Case contribution payment recorded for Member ID: {$user->id}");
                break;
                
            default:
                Log::info("Generic payment type processed for Member ID: {$user->id}");
                break;
        }
    }
}