<?php

namespace App\Services;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\RegistrationPayment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;

class BenevolenceClaimEligibilityService
{
    /**
     * Check whether a member is eligible to submit or have a benevolence claim/case opened.
     * Returns null if eligible, or a clear, concise administrative error message if ineligible.
     */
    public function check(User $user): ?string
    {
        $settings = Setting::current();
        $memberName = trim(($user->first_name ?? 'Member') . ' ' . ($user->last_name ?? ''));
        $currentYear = (int) date('Y');

        // =========================================================================
        // 1. ANNUAL REGISTRATION FEE CHECK (Chapter 2, Condition 1)
        // =========================================================================
        $hasPaidRegFee = $user->registration_fee_paid || RegistrationPayment::where('user_id', $user->id)
            ->where('year', $currentYear)
            ->where('status', 'success')
            ->exists();

        if (!$hasPaidRegFee) {
            $regFeeAmount = number_format($settings->registration_fee ?? 150);
            return "As per Chapter Two, Condition 1 of the ROSSET-SWA Constitution, {$memberName}'s annual registration fee of Ksh {$regFeeAmount} for the {$currentYear} financial year has not been paid. The fee must be cleared before an assistance claim can be processed.";
        }

        // =========================================================================
        // 2. PROFILE COMPLETION CHECK (Chapter 2, Condition 2)
        // =========================================================================
        $isProfileComplete = !empty($user->tsc_number) &&
            !empty($user->id_number) &&
            !empty($user->school) &&
            !str_starts_with($user->tsc_number, 'TSC-');

        if (!$isProfileComplete) {
            return "As per Chapter Two, Condition 2 of the ROSSET-SWA Constitution, the member's profile details (including TSC number, ID number, and school) are incomplete.";
        }

        // =========================================================================
        // 3. ACCOUNT STATUS CHECK (Chapter 3)
        // =========================================================================
        if (isset($user->status) && in_array(strtolower($user->status), ['suspended', 'terminated', 'defaulted', 'pending', 'rejected'])) {
            $statusText = ucfirst(strtolower($user->status));
            return "A benevolence case cannot be created because the account status is currently listed as {$statusText}.";
        }

        // =========================================================================
        // 4. LATE REGISTRATION WAITING PERIOD CHECK (Chapter 2, Condition 3)
        // =========================================================================
        $regPayment = RegistrationPayment::where('user_id', $user->id)
            ->where('year', $currentYear)
            ->where('status', 'success')
            ->first();

        $paymentDate = $regPayment ? Carbon::parse($regPayment->paid_at) : ($user->joined_at ? Carbon::parse($user->joined_at) : null);

        if ($paymentDate) {
            $deadlineMonth = $settings->registration_deadline_month ?? 2;
            $deadlineDay = $settings->registration_deadline_day ?? 28;
            $februaryDeadline = Carbon::create($currentYear, $deadlineMonth, $deadlineDay, 23, 59, 59);

            if ($paymentDate->greaterThan($februaryDeadline)) {
                $daysSincePayment = (int) $paymentDate->diffInDays(now());
                $waitingPeriodDays = $settings->late_registration_waiting_period_days ?? 30;

                if ($daysSincePayment < $waitingPeriodDays) {
                    $remainingDays = $waitingPeriodDays - $daysSincePayment;
                    return "As per Chapter Two, Condition 3 of the ROSSET-SWA Constitution, late registrations completed after 28th February are subject to a mandatory {$waitingPeriodDays}-day waiting period. The member has {$remainingDays} day(s) remaining before becoming eligible for welfare claims.";
                }
            }
        }

        // =========================================================================
        // 5. PARTICIPATION / CONTRIBUTION DEFAULTER CHECK (Chapter 3 & Chapter 5)
        // =========================================================================
        if ($user->joined_at) {
            $contributedCaseIds = BenevolenceContribution::where('user_id', $user->id)
                ->pluck('benevolence_case_id')
                ->toArray();

            $missedCasesCount = BenevolenceCase::whereIn('status', ['active', 'closed'])
                ->where('created_at', '>=', $user->joined_at)
                ->where('user_id', '!=', $user->id) // Exclude member's own case
                ->whereNotIn('id', $contributedCaseIds)
                ->count();

            if ($missedCasesCount > 0) {
                return "As per Chapter Three and Chapter Five of the ROSSET-SWA Constitution, records indicate the member has missed contributing to {$missedCasesCount} past benevolence case(s). Claims are restricted until all arrears are fully cleared.";
            }
        }

        return null; // Eligible!
    }
}