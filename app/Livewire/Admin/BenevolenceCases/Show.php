<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\SolidarityFund;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    // Capture Payment Modal Properties
    public $showCaptureModal = false;
    public $searchMember = '';
    public $selectedMember = null;
    public $user_id = '';
    public $amount = '';
    public $paid_at = '';
    public $referenceNumber = '';
    public $notes = '';

    public function mount($id, $slug)
    {
        $this->case = BenevolenceCase::with(['member', 'category', 'creator'])
            ->where('id', $id)
            ->where('slug', $slug)
            ->firstOrFail();

        $this->amount = $this->case->category->amount ?? 500;
        $this->paid_at = date('Y-m-d');
    }

    // Capture Payment Modal Actions
    public function openCaptureModal()
    {
        $this->reset(['searchMember', 'selectedMember', 'user_id', 'referenceNumber', 'notes']);
        $this->amount = $this->case->category->amount ?? 500;
        $this->paid_at = date('Y-m-d');
        $this->showCaptureModal = true;
        $this->resetErrorBag();
    }

    public function closeCaptureModal()
    {
        $this->showCaptureModal = false;
        $this->reset(['searchMember', 'selectedMember', 'user_id', 'referenceNumber', 'notes']);
    }

    public function selectMember($id)
    {
        $this->selectedMember = User::find($id);
        if ($this->selectedMember) {
            $this->user_id = $this->selectedMember->id;
            $this->searchMember = '';
        }
    }

    public function clearSelectedMember()
    {
        $this->selectedMember = null;
        $this->user_id = '';
    }

    public function savePayment()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'paid_at' => 'required|date',
            'referenceNumber' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ], [
            'user_id.required' => 'Please search and select a valid member.',
        ]);

        $user = User::findOrFail($this->user_id);
        $admin = Auth::user();
        $ref = !empty($this->referenceNumber) ? strtoupper(trim($this->referenceNumber)) : ('BEN-MAN-' . strtoupper(Str::random(6)));

        DB::beginTransaction();

        try {
            $tx = Transaction::create([
                'user_id' => $user->id,
                'recorded_by' => $admin->id,
                'reference_number' => $ref,
                'amount' => $this->amount,
                'type' => 'debit',
                'category' => 'benevolence_contribution',
                'status' => 'success',
                'phone_number' => $user->phone ?? 'N/A',
                'description' => "Manual admin contribution for benevolence case: {$this->case->case_number}",
            ]);

            BenevolenceContribution::create([
                'benevolence_case_id' => $this->case->id,
                'user_id' => $user->id,
                'transaction_id' => $tx->id,
                'amount' => $this->amount,
                'payment_channel' => 'Manual Admin Entry',
                'reference_number' => $ref,
                'notes' => $this->notes ?: "Captured manually by Admin {$admin->first_name}",
                'recorded_by' => $admin->id,
                'created_at' => $this->paid_at . ' ' . date('H:i:s'),
                'updated_at' => $this->paid_at . ' ' . date('H:i:s'),
            ]);

            DB::commit();

            $this->closeCaptureModal();
            $this->dispatch('swal:success', ['message' => "Benevolence contribution successfully recorded for {$user->first_name} {$user->last_name}."]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', ['message' => 'Failed to record contribution: ' . $e->getMessage()]);
        }
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

            if ($targetStatus === 'suspended' && $previousStatus !== 'suspended') {
                $contributions = BenevolenceContribution::where('benevolence_case_id', $this->case->id)
                    ->where('payment_channel', 'Solidarity Fund Wallet')
                    ->with('transaction')
                    ->get();

                foreach ($contributions as $contribution) {
                    $userWallet = SolidarityFund::where('user_id', $contribution->user_id)->lockForUpdate()->first();
                    $user = User::find($contribution->user_id);

                    if ($userWallet && $user) {
                        $userWallet->increment('balance', $contribution->amount);
                        $refNo = 'REF-' . strtoupper(Str::random(8));

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

                        if ($contribution->transaction) {
                            $contribution->transaction->update(['status' => 'refunded']);
                        }
                    }
                }

                BenevolenceContribution::where('benevolence_case_id', $this->case->id)
                    ->where('payment_channel', 'Solidarity Fund Wallet')
                    ->delete();
            }

            $this->case->update(['status' => $targetStatus]);
            $this->case->refresh();

            DB::commit();

            $this->showStatusModal = false;
            
            $message = 'Case status updated to ' . ucfirst($targetStatus) . ' successfully.';
            if ($targetStatus === 'suspended') {
                $message .= ' Solidarity fund deductions have been fully refunded to affected members.';
            }

            $this->dispatch('swal:success', ['message' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->showStatusModal = false;
            $this->dispatch('swal:error', ['message' => 'Failed to update case status: ' . $e->getMessage()]);
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
        $this->dispatch('swal:success', ['message' => 'Contribution deadline updated successfully.']);
    }

    public function render()
    {
        $searchedMembers = collect();
        if (strlen(trim($this->searchMember)) > 0) {
            $searchedMembers = User::where('first_name', 'like', '%' . $this->searchMember . '%')
                ->orWhere('last_name', 'like', '%' . $this->searchMember . '%')
                ->orWhere('membership_number', 'like', '%' . $this->searchMember . '%')
                ->orWhere('tsc_number', 'like', '%' . $this->searchMember . '%')
                ->limit(5)
                ->get();
        }

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

        // Strictly query allowed gender enum values ('male', 'female')
        $contributorsByGender = User::whereIn('id', $contributingUserIds)
            ->whereIn('gender', ['male', 'female'])
            ->selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // Strictly query allowed school level options ('Primary', 'Junior', 'Senior')
        $contributorsBySchoolLevel = User::whereIn('id', $contributingUserIds)
            ->whereIn('school_level', ['Primary', 'Junior', 'Senior'])
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
            'searchedMembers' => $searchedMembers,
        ]);
    }
}