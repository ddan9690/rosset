<?php

namespace App\Services;

use App\Models\AgmPayment;
use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\User;
use Carbon\Carbon;

class BenevolenceClaimEligibilityService
{
    /**
     * Check whether a member is eligible to submit a benevolence claim.
     * Returns null if eligible, or a polite, constitutionally-cited error message if ineligible.
     */
    public function check(User $user): ?string
    {
        // 1. Annual Registration Fee Check (Chapter 2, Condition 1)
        if (!$user->registration_fee_paid) {
            return 'As per Chapter Two, Condition 1 of the ROSSET-SWA Constitution, your annual registration fee of Ksh 150 must be fully paid before you can submit an assistance claim.';
        }

        // 2. Profile Completion Check (Chapter 2, Condition 2)
        $isProfileComplete = !empty($user->tsc_number) &&
            !empty($user->id_number) &&
            !empty($user->school) &&
            !str_starts_with($user->tsc_number, 'TSC-');

        if (!$isProfileComplete) {
            return 'As per Chapter Two, Condition 2 of the ROSSET-SWA Constitution, please ensure your member profile details (including your TSC number, ID number, and school) are completely updated in your portal.';
        }

        // 3. Active Account Status Check (Chapter 3)
        if (isset($user->status) && in_array(strtolower($user->status), ['suspended', 'terminated', 'defaulted', 'pending', 'rejected'])) {
            return 'As per Chapter Three of the ROSSET-SWA Constitution, your current membership account status (' . ucfirst($user->status) . ') restricts you from submitting assistance claims at this time.';
        }

        // 4. AGM Fee Payment Check for Current Financial Year (Chapter 6 & Chapter 10)
        $currentYear = (int) date('Y');
        $hasPaidAgm = AgmPayment::where('user_id', $user->id)
            ->where('year', $currentYear)
            ->whereIn('status', ['success', 'paid', 'completed', 'approved', 'successful'])
            ->exists();

        if (!$hasPaidAgm) {
            return "As per Chapter Six and Chapter Ten of the ROSSET-SWA Constitution, members are required to pay the mandatory Ksh 150 AGM contribution fee for the {$currentYear} financial year before accessing welfare benefits.";
        }

        // 5. Late Registration Waiting Period Check (Chapter 2, Condition 3)
        if ($user->joined_at) {
            $joinedDate = Carbon::parse($user->joined_at);
            $februaryDeadline = Carbon::create($joinedDate->year, 2, 28, 23, 59, 59);

            if ($joinedDate->greaterThan($februaryDeadline)) {
                $daysSinceJoined = (int) $joinedDate->diffInDays(now());
                if ($daysSinceJoined < 30) {
                    $remainingDays = 30 - $daysSinceJoined;
                    return "As per Chapter Two, Condition 3 of the ROSSET-SWA Constitution, late registrations completed after 28th February are subject to a mandatory 30-day waiting period. You have {$remainingDays} day(s) remaining before becoming eligible.";
                }
            }
        }

        // 6. Participation / Contribution Defaulter Check (Chapter 3 & Chapter 5)
        if ($user->joined_at) {
            $contributedCaseIds = BenevolenceContribution::where('user_id', $user->id)
                ->pluck('benevolence_case_id')
                ->toArray();

            $missedCasesCount = BenevolenceCase::whereIn('status', ['active', 'closed'])
                ->where('created_at', '>=', $user->joined_at)
                ->where('user_id', '!=', $user->id) // Exclude own case
                ->whereNotIn('id', $contributedCaseIds)
                ->count();

            if ($missedCasesCount > 0) {
                return "As per Chapter Three and Chapter Five of the ROSSET-SWA Constitution, our records indicate you have missed contributing to {$missedCasesCount} past benevolence case(s). Defaulting on contributions temporarily disqualifies members from submitting claims until all arrears are fully cleared in solidarity with colleagues.";
            }
        }

        return null; 
    }
}