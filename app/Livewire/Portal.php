<?php

namespace App\Livewire;

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

    public $solidarityBalance = 148500;

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
            $this->membershipNumber = $user->tsc_number ?? 'N/A';
            $this->memberStatus = ucfirst($user->status ?? 'Pending');
            
            // Check if registration fee is paid
            $this->isRegistrationPaid = (bool) ($user->registration_fee_paid ?? false);
        }
    }

    public function payRegistrationFee()
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'registration_fee_paid' => true,
                'status' => 'active'
            ]);
            $this->isRegistrationPaid = true;
            $this->memberStatus = 'Active';
        }

        session()->flash('message', 'Registration fee of KES 200 successfully received. Welcome to the full portal!');
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