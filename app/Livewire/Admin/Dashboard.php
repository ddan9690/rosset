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
        // Member Statistics & Breakdowns (excluding developers)
        $totalMembers = User::excludeDevelopers()->count();
        $maleMembers = User::excludeDevelopers()->where('gender', 'male')->count();
        $femaleMembers = User::excludeDevelopers()->where('gender', 'female')->count();
        $juniorSchoolMembers = User::excludeDevelopers()->where('school_level', 'Junior School')->count();
        $seniorSchoolMembers = User::excludeDevelopers()->where('school_level', 'Senior School')->count();

        // Status-based Member Counts & Pending Membership Requests (excluding developer requests/users)
        $pendingRequestsCount = MembershipRequest::whereHas('user', function ($q) {
            $q->excludeDevelopers();
        })->where('status', 'pending')->count();

        $defaultersCount = User::excludeDevelopers()->where('status', 'defaulted')->count();
        $suspendedCount = User::excludeDevelopers()->where('status', 'suspended')->count();
        $deregisteredCount = User::excludeDevelopers()->where('status', 'deregistered')->count();

        // Benevolence & Financial Summaries
        $benevolenceCasesCount = BenevolenceCase::whereHas('member', function ($q) {
            $q->excludeDevelopers();
        })->count();

        // Total solidarity fund balance strictly for non-developer members
        $totalSolidarityBalance = SolidarityFund::whereHas('user', function ($q) {
            $q->excludeDevelopers();
        })->sum('balance');

        // Total active registered members for contribution progress ratio denominator
        $totalActiveMembers = User::excludeDevelopers()->where('status', 'active')->count();

        // Benevolence Cases with total collected & contributor counts (excluding developers from cases & contributors)
        $benevolenceCases = BenevolenceCase::whereHas('member', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['member' => function ($q) {
                $q->excludeDevelopers();
            }, 'category'])
            ->withCount([
                'transactions as contributors_count' => function ($query) {
                    $query->where('type', 'benevolence_contribution')
                        ->where('status', 'success')
                        ->whereHas('user', function ($q) {
                            $q->excludeDevelopers();
                        });
                }
            ])
            ->withSum([
                'transactions as total_collected' => function ($query) {
                    $query->where('type', 'benevolence_contribution')
                        ->where('status', 'success')
                        ->whereHas('user', function ($q) {
                            $q->excludeDevelopers();
                        });
                }
            ], 'amount')
            ->latest()
            ->take(5)
            ->get();

        // Recent Gateway Transactions (excluding developer transactions)
        $recentTransactions = Transaction::whereHas('user', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['user' => function ($q) {
                $q->excludeDevelopers();
            }])
            ->latest('created_at')
            ->take(20)
            ->get()
            ->each(function ($transaction) {
                if ($transaction->created_at) {
                    $transaction->created_at = $transaction->created_at->setTimezone('Africa/Nairobi');
                }
            });

        // Recent Transaction Ledgers (excluding developer ledgers)
        $recentLedgers = TransactionLedger::whereHas('user', function ($q) {
                $q->excludeDevelopers();
            })
            ->with(['user' => function ($q) {
                $q->excludeDevelopers();
            }])
            ->latest()
            ->take(10)
            ->get();

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