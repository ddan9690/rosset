<?php

namespace App\Livewire;

use App\Models\SolidarityFund;
use App\Models\BenevolenceCase;
use App\Models\Transaction;
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

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Member Details
        |--------------------------------------------------------------------------
        */

        $this->memberName = trim(
            ($user->salutation ?? '') .
            ' ' .
            ($user->first_name ?? '') .
            ' ' .
            ($user->last_name ?? '')
        );

        $this->membershipNumber =
            $user->tsc_number
            ??
            $user->membership_number
            ??
            'N/A';

        $this->memberStatus =
            ucfirst(
                $user->status ?? 'Pending'
            );

        $this->isRegistrationPaid =
            (bool) (
                $user->registration_fee_paid ?? false
            );

        /*
        |--------------------------------------------------------------------------
        | Profile Complete
        |--------------------------------------------------------------------------
        */

        $this->isProfileComplete =
            !empty($user->tsc_number)
            &&
            !empty($user->id_number)
            &&
            !empty($user->school)
            &&
            !str_starts_with(
                $user->tsc_number,
                'TSC-'
            );

        /*
        |--------------------------------------------------------------------------
        | Solidarity Wallet
        |--------------------------------------------------------------------------
        */

        $wallet =
            SolidarityFund::firstOrCreate(
                [
                    'user_id' =>
                        $user->id,
                ]
            );

        $this->solidarityBalance =
            $wallet->balance ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Registration Payment
    |--------------------------------------------------------------------------
    */

    public function redirectToPayment()
    {
        return redirect()->route(
            'register.fee'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Benevolence Contribution
    |--------------------------------------------------------------------------
    */

    public function sendContribution(
        $caseId
    ) {
        return redirect()->route(
            'benevolence.contribute',
            [
                'id' => $caseId,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Active Cases
        |--------------------------------------------------------------------------
        */

        $activeCases =
            BenevolenceCase::with([
                'member',
                'category',
            ])
            ->where(
                'status',
                'active'
            )
            ->oldest('created_at')
            ->get()
            ->map(
                function ($case) use ($user) {

                    /*
                    |--------------------------------------------------------------------------
                    | Check Successful Contribution
                    |--------------------------------------------------------------------------
                    */

                    $case->contribution_made =
                        Transaction::query()
                            ->where(
                                'user_id',
                                $user->id
                            )
                            ->where(
                                'case_number',
                                $case->case_number
                            )
                            ->where(
                                'type',
                                'benevolence_contribution'
                            )
                            ->where(
                                'status',
                                'success'
                            )
                            ->exists();

                    return $case;
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Contribution History
        |--------------------------------------------------------------------------
        |
        | Only successful benevolence contributions belonging to the
        | currently logged-in member are displayed.
        |
        */

        $contributionHistory =
            Transaction::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'type',
                    'benevolence_contribution'
                )
                ->where(
                    'status',
                    'success'
                )
                ->whereNotNull('paid_at')
                ->latest('paid_at')
                ->get();

        return view(
            'livewire.portal',
            [
                'benevolenceCases' =>
                    $activeCases,

                'contributionHistory' =>
                    $contributionHistory,
            ]
        );
    }
}