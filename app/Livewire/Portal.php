<?php

namespace App\Livewire;

use App\Models\SolidarityFund;
use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\MembershipRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Portal extends Component
{
    public $title = 'Member Portal | ROSSET-SWA';
    public $memberName = '';
    public $membershipNumber = '';
    public $memberStatus = '';
    public $isRegistrationPaid = false;
    public $isProfileComplete = false;
    public $solidarityBalance = 0;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow previously onboarded or active members to bypass the membership request check
        if ($user->status === 'active' || $user->registration_fee_paid) {
            // Fully onboarded, proceed to portal
        } else {
            $membershipReq = MembershipRequest::where('user_id', $user->id)->latest()->first();

            if (!$membershipReq || in_array($membershipReq->status, ['pending', 'rejected'])) {
                return redirect()->route('membership.status');
            }

            if ($membershipReq->status === 'approved' && !$user->registration_fee_paid) {
                return redirect()->route('register.fee');
            }
        }

        $this->memberName = trim(
            ($user->salutation ?? '') . ' ' .
                ($user->first_name ?? '') . ' ' .
                ($user->last_name ?? '')
        );

        $this->membershipNumber = $user->membership_number ?? $user->tsc_number ?? 'N/A';
        $this->memberStatus = ucfirst($user->status ?? 'Pending');
        $this->isRegistrationPaid = (bool) ($user->registration_fee_paid ?? false);

        $this->isProfileComplete =
            !empty($user->tsc_number) &&
            !empty($user->id_number) &&
            !empty($user->school) &&
            !str_starts_with($user->tsc_number, 'TSC-');

        $wallet = SolidarityFund::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $this->solidarityBalance = $wallet->balance ?? 0;
    }

    public function redirectToPayment()
    {
        return redirect()->route('register.fee');
    }

    public function sendContribution($caseId)
    {
        return redirect()->route('benevolence.contribute', [
            'id' => $caseId,
        ]);
    }

    public function downloadContributionsPdf()
    {
        return redirect()->route('portal.pdf.contributions');
    }

    public function render()
    {
        $user = Auth::user();

        $activeCases = BenevolenceCase::with(['member', 'category'])
            ->where('status', 'active')
            ->oldest('created_at')
            ->get()
            ->map(function ($case) use ($user) {
                // Check if a contribution record already exists for this user and case
                $case->contribution_made = BenevolenceContribution::query()
                    ->where('user_id', $user->id)
                    ->where('benevolence_case_id', $case->id)
                    ->exists();

                return $case;
            });

        $contributionHistory = BenevolenceContribution::query()
            ->with(['benevolenceCase.member'])
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->get();

        return view('livewire.portal', [
            'benevolenceCases' => $activeCases,
            'contributionHistory' => $contributionHistory,
        ]);
    }
}