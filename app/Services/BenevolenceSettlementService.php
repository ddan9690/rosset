<?php

namespace App\Services;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\SolidarityFund;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BenevolenceSettlementService
{
    /**
     * Settle eligible members from their solidarity fund for a given benevolence case.
     * 
     * @param BenevolenceCase $case
     * @param callable|null $progressCallback Receives ($current, $total, $message)
     * @return int Number of successfully settled members
     */
    public function settleEligibleMembers(BenevolenceCase $case, ?callable $progressCallback = null): int
    {
        $categoryAmount = $case->category->amount ?? 0;

        if ($categoryAmount <= 0) {
            return 0;
        }

        // 1. Get IDs of members who have already contributed or are the case owner
        $excludedUserIds = BenevolenceContribution::where('benevolence_case_id', $case->id)
            ->pluck('user_id')
            ->push($case->user_id)
            ->unique();

        // 2. Fetch active members with sufficient solidarity fund balance who aren't excluded
        $eligibleUsers = User::where('status', 'active')
            ->whereNotIn('id', $excludedUserIds)
            ->with('solidarityFund')
            ->get()
            ->filter(function ($user) use ($categoryAmount) {
                return $user->solidarityFund && $user->solidarityFund->balance >= $categoryAmount;
            })
            ->values();

        $totalCount = $eligibleUsers->count();
        if ($totalCount === 0) {
            return 0;
        }

        $settledCount = 0;

        if ($progressCallback) {
            $progressCallback(0, $totalCount, "Preparing solidarity fund deductions...");
        }


        DB::transaction(function () use ($eligibleUsers, $case, $categoryAmount, $totalCount, &$settledCount, $progressCallback) {
            foreach ($eligibleUsers as $index => $user) {
                $wallet = SolidarityFund::where('user_id', $user->id)->lockForUpdate()->first();

                if (!$wallet || $wallet->balance < $categoryAmount) {
                    // Skip if balance changed concurrently or insufficient
                    continue;
                }

                // Deduct wallet balance
                $wallet->decrement('balance', $categoryAmount);

                $refNo = 'SOL-' . strtoupper(Str::random(8));

               
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $categoryAmount,
                    'type' => 'debit',
                    'category' => 'solidarity_deduction',
                    'reference_number' => $refNo,
                    'status' => 'success',
                    'phone_number' => $user->phone ?? null, 
                    'description' => "Solidarity deduction for case: {$case->case_number}",
                ]);

                // Record contribution
                BenevolenceContribution::create([
                    'benevolence_case_id' => $case->id,
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id ?? null,
                    'amount' => $categoryAmount,
                    'payment_channel' => 'Solidarity Fund Wallet',
                    'reference_number' => $refNo,
                    'notes' => 'Automatic solidarity fund settlement',
                    'recorded_by' => auth()->id(),
                ]);

                $settledCount++;

                if ($progressCallback) {
                    $current = $index + 1;
                    $message = "Deducting from member {$user->membership_number} ({$user->first_name} {$user->last_name})...";
                    $progressCallback($current, $totalCount, $message);
                }
            }
        });

        return $settledCount;
    }
}
