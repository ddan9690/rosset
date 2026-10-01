<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\MembershipRequest;
use App\Models\BenevolenceCase;
use App\Models\Transaction;
use App\Models\TransactionLedger;
use App\Models\SolidarityFund;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Admin Dashboard | ROSSET-SWA')]
class Dashboard extends Component
{
    public function render()
    {
        // Member Statistics & Breakdowns
        $totalMembers = User::count();
        $maleMembers = User::where('gender', 'male')->count();
        $femaleMembers = User::where('gender', 'female')->count();
        $juniorSchoolMembers = User::where('school_level', 'Junior School')->count();
        $seniorSchoolMembers = User::where('school_level', 'Senior School')->count();

        // Status-based Member Counts & Pending Membership Requests
        $pendingRequestsCount = MembershipRequest::where('status', 'pending')->count();
        $defaultersCount = User::where('status', 'defaulted')->count();
        $suspendedCount = User::where('status', 'suspended')->count();
        $deregisteredCount = User::where('status', 'deregistered')->count();

        // Benevolence & Financial Summaries
        $benevolenceCasesCount = BenevolenceCase::count();
        $totalSolidarityBalance = SolidarityFund::sum('balance');

        // Total active registered members for contribution progress ratio denominator
        $totalActiveMembers = User::where('status', 'active')->count();

        // Benevolence Cases with total collected & contributor counts
        $benevolenceCases = BenevolenceCase::with(['member', 'category'])
            ->withCount(['transactions as contributors_count' => function ($query) {
                $query->where('type', 'benevolence_contribution')->where('status', 'success');
            }])
            ->withSum(['transactions as total_collected' => function ($query) {
                $query->where('type', 'benevolence_contribution')->where('status', 'success');
            }], 'amount')
            ->latest()
            ->take(5)
            ->get();

        // Recent Transactions & Ledgers
        $recentTransactions = Transaction::with('user')->latest()->take(10)->get();
        $recentLedgers = TransactionLedger::with('user')->latest()->take(10)->get();

        return view('livewire.admin.dashboard', [
            'totalMembers' => $totalMembers,
            'maleMembers' => $maleMembers,
            'femaleMembers' => $femaleMembers,
            'juniorSchoolMembers' => $juniorSchoolMembers,
            'seniorSchoolMembers' => $seniorSchoolMembers,
            'pendingRequestsCount' => $pendingRequestsCount,
            'defaultersCount' => $defaultersCount,
            'suspendedCount' => $suspendedCount,
            'deregisteredCount' => $deregisteredCount,
            'benevolenceCasesCount' => $benevolenceCasesCount,
            'totalSolidarityBalance' => $totalSolidarityBalance,
            'totalActiveMembers' => $totalActiveMembers,
            'benevolenceCases' => $benevolenceCases,
            'recentTransactions' => $recentTransactions,
            'recentLedgers' => $recentLedgers,
        ]);
    }
}