<?php

namespace App\Livewire\Admin;

use App\Models\SolidarityFund;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Solidarity Fund Management | Admin ROSSET-SWA')]
class SolidarityManagement extends Component
{
    use WithPagination;

    public $showTopUpModal = false;
    public $search = '';
    
    public $searchMember = '';
    public $selectedMember = null;
    public $user_id = '';
    public $amount = '';
    public $notes = '';

    // Sorting properties
    public $sortField = 'membership_number';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openTopUpModal()
    {
        $this->reset(['searchMember', 'selectedMember', 'user_id', 'amount', 'notes']);
        $this->showTopUpModal = true;
        $this->resetErrorBag();
    }

    public function closeTopUpModal()
    {
        $this->showTopUpModal = false;
        $this->reset(['searchMember', 'selectedMember', 'user_id', 'amount', 'notes']);
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

    public function saveTopUp()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ], [
            'user_id.required' => 'Please search and select a member.',
            'amount.required' => 'Please enter a valid amount.',
        ]);

        $amount = (float) $this->amount;

        $wallet = SolidarityFund::firstOrNew(['user_id' => $this->user_id]);
        
        if (!$wallet->exists) {
            $wallet->balance = 0;
            $wallet->total_topups = 0;
            $wallet->total_deductions = 0;
            $wallet->recorded_by = Auth::id();
        }

        $wallet->balance += $amount;
        $wallet->total_topups += $amount;
        $wallet->notes = $this->notes ? trim($this->notes) : $wallet->notes;
        $wallet->updated_by = Auth::id();
        $wallet->save();

        $memberName = trim(($wallet->user->first_name ?? '') . ' ' . ($wallet->user->last_name ?? ''));

        $this->closeTopUpModal();

        $this->dispatch('swal:success', [
            'message' => "Successfully credited Ksh " . number_format($amount, 0) . " to {$memberName}'s Solidarity Fund."
        ]);
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

        // Query strictly filtering for balances greater than 0
        $query = SolidarityFund::with('user')
            ->join('users', 'solidarity_funds.user_id', '=', 'users.id')
            ->where('solidarity_funds.balance', '>', 0)
            ->select('solidarity_funds.*');

        if (strlen(trim($this->search)) > 0) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('users.membership_number', 'like', $searchTerm)
                  ->orWhere('users.first_name', 'like', $searchTerm)
                  ->orWhere('users.last_name', 'like', $searchTerm);
            });
        }

        if ($this->sortField === 'membership_number') {
            $query->orderBy('users.membership_number', $this->sortDirection);
        } elseif ($this->sortField === 'balance') {
            $query->orderBy('solidarity_funds.balance', $this->sortDirection);
        }

        $solidarityAccounts = $query->paginate(10);

        return view('livewire.admin.solidarity-management', [
            'solidarityAccounts' => $solidarityAccounts,
            'searchedMembers' => $searchedMembers,
            'totalSolidarityBalance' => SolidarityFund::sum('balance'),
        ]);
    }
}