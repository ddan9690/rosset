<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceContribution;
use App\Models\User;
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
        // Fetch all contributions for this case using benevolence_case_id
        $contributions = BenevolenceContribution::where('benevolence_case_id', $this->case->id)
            ->with('user')
            ->latest('created_at')
            ->get();

        // Total unique contributors
        $contributingUserIds = $contributions->pluck('user_id')->unique();
        $totalContributorsCount = $contributingUserIds->count();

        // Total members in system for percentage calculation
        $totalSystemMembers = User::count();
        $contributionPercentage = $totalSystemMembers > 0 
            ? round(($totalContributorsCount / $totalSystemMembers) * 100, 2) 
            : 0;

        // Total funds collected for this case
        $totalAmountCollected = $contributions->sum('amount');

        // Breakdown by Gender
        $contributorsByGender = User::whereIn('id', $contributingUserIds)
            ->selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // Breakdown by School Level
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