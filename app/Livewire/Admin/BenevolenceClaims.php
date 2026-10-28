<?php

namespace App\Livewire\Admin;

use App\Models\BenevolenceClaim;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Benevolence Claims | Admin ROSSET-SWA')]
class BenevolenceClaims extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $selectedClaim = null;
    public $showViewModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewClaim($id)
    {
        $this->selectedClaim = BenevolenceClaim::with('user')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedClaim = null;
    }

    public function updateStatus($id, $status)
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return;
        }

        $claim = BenevolenceClaim::findOrFail($id);
        $claim->update(['status' => $status]);

        if ($this->selectedClaim && $this->selectedClaim->id === $id) {
            $this->selectedClaim->refresh();
        }

        session()->flash('message', 'Claim status updated to ' . ucfirst($status) . ' successfully.');
    }

    public function render()
    {
        $query = BenevolenceClaim::with('user')->latest('created_at');

        if (!empty($this->search)) {
            $searchTerm = trim($this->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%")
                            ->orWhere('membership_number', 'like', "%{$searchTerm}%")
                            ->orWhere('tsc_number', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $claims = $query->paginate(10);

        return view('livewire.admin.benevolence-claims', [
            'claims' => $claims,
        ]);
    }
}
