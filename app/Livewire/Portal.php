<?php

namespace App\Livewire;

use App\Models\SolidarityFund;
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

    public $benevolenceCases = [
        [
            'case_number' => 'BEN-2026-001',
            'member_name' => 'John Ochieng',
            'membership_number' => 'TSC/128492',
            'category' => 'Self (Member Bereavement)',
            'bereaved_person' => 'Self',
            'amount' => 500,
            'burial_date' => '2026-10-05',
            'deadline' => '2026-10-02',
            'contribution_made' => false,
        ],
        [
            'case_number' => 'BEN-2026-002',
            'member_name' => 'Mary Akoth',
            'membership_number' => 'TSC/984211',
            'category' => 'Spouse Bereavement',
            'bereaved_person' => 'Spouse',
            'amount' => 300,
            'burial_date' => '2026-10-12',
            'deadline' => '2026-10-09',
            'contribution_made' => true,
        ],
    ];

    public $recentTransactions = [
        [
            'case_number' => 'BEN-2026-002',
            'member_name' => 'Mary Akoth',
            'membership_number' => 'TSC/984211',
            'amount_paid' => 300,
            'date_paid' => '2026-09-15',
        ],
    ];

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

            // Fetch live solidarity fund balance from database
            $wallet = SolidarityFund::firstOrCreate(['user_id' => $user->id]);
            $this->solidarityBalance = $wallet->balance;
        }
    }

    public function redirectToPayment()
    {
        return redirect()->route('register.fee');
    }

    public function sendContribution($caseNumber)
    {
        foreach ($this->benevolenceCases as &$case) {
            if ($case['case_number'] === $caseNumber) {
                $case['contribution_made'] = true;
                
                array_unshift($this->recentTransactions, [
                    'case_number' => $case['case_number'],
                    'member_name' => $case['member_name'],
                    'membership_number' => $case['membership_number'],
                    'amount_paid' => $case['amount'],
                    'date_paid' => now()->format('Y-m-d'),
                ]);
            }
        }
        
        session()->flash('message', 'Contribution successfully processed for case ' . $caseNumber);
    }

    public function render()
    {
        return view('livewire.portal');
    }
}