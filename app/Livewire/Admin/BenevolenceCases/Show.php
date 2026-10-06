<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\SolidarityFund;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('View Benevolence Case | Admin ROSSET-SWA')]
class Show extends Component
{
    public $case;

    // Modal States
    public $showStatusModal = false;
    public $selectedStatus = '';

    public $showDeadlineModal = false;
    public $newDeadline = '';

    public function mount($id, $slug)
    {
        $this->case = BenevolenceCase::with(['member', 'category', 'creator'])
            ->where('id', $id)
            ->where('slug', $slug)
            ->firstOrFail();
    }

    // Status Modal Actions
    public function openStatusModal($status)
    {
        $this->selectedStatus = $status;
        $this->showStatusModal = true;
    }

    public function updateStatus()
    {
        if (!in_array($this->selectedStatus, ['active', 'suspended', 'closed'])) {
            return;
        }

        DB::beginTransaction();

        try {
            $previousStatus = $this->case->status;
            $targetStatus = $this->selectedStatus;

            // Handle Suspension / Solidarity Fund Refund Logic
            if ($targetStatus === 'suspended' && $previousStatus !== 'suspended') {
                // Fetch contributions made specifically via Solidarity Fund Wallet for this case
                $contributions = BenevolenceContribution::where('benevolence_case_id', $this->case->id)
                    ->where('payment_channel', 'Solidarity Fund Wallet')
                    ->with('transaction')
                    ->get();

                foreach ($contributions as $contribution) {
                    $userWallet = SolidarityFund::where('user_id', $contribution->user_id)->lockForUpdate()->first();
                    $user = User::find($contribution->user_id);

                    if ($userWallet && $user) {
                        // 1. Refund/increment back the solidarity fund balance
                        $userWallet->increment('balance', $contribution->amount);

                        $refNo = 'REF-' . strtoupper(Str::random(8));

                        // 2. Record refund transaction log
                        Transaction::create([
                            'user_id' => $user->id,
                            'amount' => $contribution->amount,
                            'type' => 'credit',
                            'category' => 'solidarity_refund',
                            'reference_number' => $refNo,
                            'status' => 'success',
                            'phone_number' => $user->phone ?? null,
                            'description' => "Solidarity refund for suspended case: {$this->case->case_number}",
                        ]);

                        // Optional: Mark the original transaction as reversed if tracked
                        if ($contribution->transaction) {
                            $contribution->transaction->update(['status' => 'refunded']);
                        }
                    }
                }

                // Optional: Delete or mark solidarity contributions as reversed/deleted
                BenevolenceContribution::where('benevolence_case_id', $this->case->id)
                    ->where('payment_channel', 'Solidarity Fund Wallet')
                    ->delete();
            }

            // Update case status
            $this->case->update(['status' => $targetStatus]);
            $this->case->refresh();

            DB::commit();

            $this->showStatusModal = false;
            
            $message = 'Case status updated to ' . ucfirst($targetStatus) . ' successfully.';
            if ($targetStatus === 'suspended') {
                $message .= ' Solidarity fund deductions have been fully refunded to affected members.';
            }

            session()->flash('message', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->showStatusModal = false;
            session()->flash('error', 'Failed to update case status: ' . $e->getMessage());
        }
    }

    // Deadline Modal Actions
    public function openDeadlineModal()
    {
        $this->newDeadline = $this->case->deadline;
        $this->showDeadlineModal = true;
    }

    public function updateDeadline()
    {
        $this->validate([
            'newDeadline' => 'required|date',
        ]);

        $this->case->update(['deadline' => $this->newDeadline]);
        $this->case->refresh();

        $this->showDeadlineModal = false;
        session()->flash('message', 'Contribution deadline updated successfully.');
    }

    public function render()
    {
        $contributions = BenevolenceContribution::where('benevolence_case_id', $this->case->id)
            ->with('user')
            ->latest('created_at')
            ->get();

        $contributingUserIds = $contributions->pluck('user_id')->unique();
        $totalContributorsCount = $contributingUserIds->count();

        $totalSystemMembers = User::count();
        $contributionPercentage = $totalSystemMembers > 0 
            ? round(($totalContributorsCount / $totalSystemMembers) * 100, 2) 
            : 0;

        $totalAmountCollected = $contributions->sum('amount');

        $contributorsByGender = User::whereIn('id', $contributingUserIds)
            ->selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        $contributorsBySchoolLevel = User::whereIn('id', $contributingUserIds)
            ->selectRaw('school_level, count(*) as count')
            ->groupBy('school_level')
            ->pluck('count', 'school_level')
            ->toArray();

        return view('livewire.admin.benevolence-cases.show', [
            'contributions' => $contributions,
            'totalContributorsCount' => $totalContributorsCount,
            'totalSystemMembers' => $totalSystemMembers,
            'contributionPercentage' => $contributionPercentage,
            'totalAmountCollected' => $totalAmountCollected,
            'contributorsByGender' => $contributorsByGender,
            'contributorsBySchoolLevel' => $contributorsBySchoolLevel,
        ]);
    }
}