<?php

namespace App\Services;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\SolidarityFund;
use App\Models\User;

class WelfareRatingService
{
    /**
     * Calculate a comprehensive, fair, and objective welfare rating score (0 - 100%) for a user.
     *
     * @param User $user
     * @return array
     */
    public function calculateRating(User $user): array
    {
        $eligibleQuery = BenevolenceCase::whereIn('status', ['active', 'closed', 'suspended']);
        
        if ($user->joined_at) {
            $eligibleQuery->where('created_at', '>=', $user->joined_at);
        }
        
        $eligibleCases = $eligibleQuery->where('user_id', '!=', $user->id)->get();
        $totalEligibleCount = $eligibleCases->count();

        if ($totalEligibleCount === 0) {
            return [
                'score' => 100,
                'tier' => 'Exemplary Pillar',
                'badge_color' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                'total_eligible' => 0,
                'contributed_count' => 0,
                'punctual_count' => 0,
                'solidarity_bonus' => 0,
            ];
        }

        $userContributions = BenevolenceContribution::where('user_id', $user->id)
            ->get()
            ->keyBy('benevolence_case_id');

        $contributedCount = 0;
        $punctualCount = 0;
        $totalEarnedPoints = 0;

        foreach ($eligibleCases as $case) {
            if ($userContributions->has($case->id)) {
                $contributedCount++;
                $contribution = $userContributions->get($case->id);
                $paidAt = $contribution->created_at;
                $deadline = $case->deadline ? \Carbon\Carbon::parse($case->deadline) : null;

                if ($deadline && $paidAt && $paidAt->lessThanOrEqualTo($deadline->endOfDay())) {
                    $punctualCount++;
                    $totalEarnedPoints += 1.0; 
                } else {
                    $totalEarnedPoints += 0.7; 
                }
            }
        }

        $baseRatio = $totalEarnedPoints / $totalEligibleCount;
        $baseScore = $baseRatio * 85;

        // Solidarity Wallet Commitment Bonus (Up to +15%)
        $wallet = SolidarityFund::where('user_id', $user->id)->first();
        $solidarityBonus = 0;

        if ($wallet) {
            if ($wallet->balance >= 1000) {
                $solidarityBonus = 15;
            } elseif ($wallet->balance > 0) {
                $solidarityBonus = 10;
            } elseif ($wallet->total_topups > 0) {
                $solidarityBonus = 5;
            }
        }

        $finalScore = (int) min(100, max(0, round($baseScore + $solidarityBonus)));

        // Welfare-themed Tiering & Conditional Colors
        if ($finalScore >= 90) {
            $tier = 'Exemplary Pillar';
            $badgeColor = 'bg-emerald-50 text-emerald-800 border-emerald-300';
        } elseif ($finalScore >= 75) {
            $tier = 'Reliable Contributor';
            $badgeColor = 'bg-blue-50 text-blue-800 border-blue-300';
        } elseif ($finalScore >= 50) {
            $tier = 'Active Member';
            $badgeColor = 'bg-amber-50 text-amber-800 border-amber-300';
        } else {
            $tier = 'At-Risk Status';
            $badgeColor = 'bg-red-50 text-red-800 border-red-300';
        }

        return [
            'score' => $finalScore,
            'tier' => $tier,
            'badge_color' => $badgeColor,
            'total_eligible' => $totalEligibleCount,
            'contributed_count' => $contributedCount,
            'punctual_count' => $punctualCount,
            'solidarity_bonus' => $solidarityBonus,
        ];
    }
}