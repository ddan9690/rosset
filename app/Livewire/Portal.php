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

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->memberName = trim(($user->salutation ?? '') . ' ' . $user->first_name . ' ' . $user->last_name);
            $this->membershipNumber = $user->tsc_number ?? $user->membership_number ?? 'N/A';
            $this->memberStatus = ucfirst($user->status ?? 'Pending');
            
            $this->isRegistrationPaid = (bool) ($user->registration_fee_paid ?? false);
            
            $this->isProfileComplete = !empty($user->tsc_number) 
                && !empty($user->id_number) 
                && !empty($user->school) 
                && !str_starts_with($user->tsc_number, 'TSC-');

            $wallet = SolidarityFund::firstOrCreate(['user_id' => $user->id]);
            $this->solidarityBalance = $wallet->balance;
        }
    }

    public function redirectToPayment()
    {
        return redirect()->route('register.fee');
    }

    public function sendContribution($caseId)
    {
        return redirect()->route('benevolence.contribute', ['id' => $caseId]);
    }

    public function render()
    {
        $user = Auth::check() ? Auth::user() : null;

        $activeCases = BenevolenceCase::with(['member', 'category'])
            ->where('status', 'active')
            ->oldest('created_at')
            ->get()
            ->map(function ($case) use ($user) {
                $case->contribution_made = $user ? Transaction::where('user_id', $user->id)
                    ->where('case_number', $case->case_number)
                    ->where('type', 'benevolence_contribution')
                    ->where('status', 'success')
                    ->exists() : false;
                return $case;
            });

        return view('livewire.portal', [
            'benevolenceCases' => $activeCases,
        ]);
    }
}