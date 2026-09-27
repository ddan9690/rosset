<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
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
        if (in_array($this->selectedStatus, ['active', 'suspended', 'closed'])) {
            $this->case->update(['status' => $this->selectedStatus]);
            $this->case->refresh();

            $this->showStatusModal = false;
            session()->flash('message', 'Case status updated to ' . ucfirst($this->selectedStatus) . ' successfully.');
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
        return view('livewire.admin.benevolence-cases.show');
    }
}