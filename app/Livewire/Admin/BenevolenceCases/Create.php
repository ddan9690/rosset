<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceCategory;
use App\Models\User;
use App\Services\BenevolenceSettlementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Create Benevolence Case | Admin ROSSET-SWA')]
class Create extends Component
{
    public $searchMember = '';
    public $selectedMember = null;
    public $user_id = '';
    
    public $benevolence_category_id = '';
    public $case_details = '';
    public $deadline = '';
    public $auto_settle = true; // Default to checked

    // Multi-step modal progress properties
    public $isProcessing = false;
    public $activeStep = 1; // 1: Opening Case, 2: Deducting Solidarity
    public $progressPercentage = 0;
    public $progressMessage = '';
    public $settledCount = 0;

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'benevolence_category_id' => 'required|exists:benevolence_categories,id',
            'case_details' => 'required|string',
            'deadline' => 'required|date',
            'auto_settle' => 'boolean',
        ];
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
        $this->benevolence_category_id = '';
        $this->case_details = '';
        $this->deadline = '';
        $this->auto_settle = true;
    }

    public function save()
    {
        $this->validate();

        $this->isProcessing = true;
        $memberName = trim(($this->selectedMember->first_name ?? 'Member') . ' ' . ($this->selectedMember->last_name ?? ''));
        $membershipNo = $this->selectedMember->membership_number ?? $this->selectedMember->id;

        // =========================================================================
        // STEP 1: SIMULATE OPENING CASE PROCESS
        // =========================================================================
        $this->activeStep = 1;
        $this->progressPercentage = 10;
        $this->progressMessage = "Validating member credentials and category parameters...";
        usleep(350000); 

        $this->progressPercentage = 30;
        $this->progressMessage = "Generating secure case sequence and unique identifiers...";
        usleep(350000);

        $latestCase = BenevolenceCase::latest('id')->first();
        $globalSequence = $latestCase ? $latestCase->id + 1 : 1;
        $caseNumber = $membershipNo . '/' . $globalSequence;
        $slug = Str::slug($memberName . '-' . $membershipNo . '-' . $globalSequence);

        $this->progressPercentage = 60;
        $this->progressMessage = "Persisting benevolence case record ({$caseNumber})...";
        usleep(300000);

        $case = BenevolenceCase::create([
            'case_number' => $caseNumber,
            'slug' => $slug,
            'user_id' => $this->user_id,
            'benevolence_category_id' => $this->benevolence_category_id,
            'case_details' => $this->case_details,
            'deadline' => $this->deadline,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        $this->progressPercentage = 100;
        $this->progressMessage = "Benevolence case successfully opened!";
        usleep(400000);

        // =========================================================================
        // STEP 2: SOLIDARITY FUND DEDUCTIONS (IF ENABLED)
        // =========================================================================
        if ($this->auto_settle) {
            $this->activeStep = 2;
            $this->progressPercentage = 0;
            $this->progressMessage = "Scanning active wallets for eligible member contributions...";
            usleep(300000);

            $service = new BenevolenceSettlementService();
            
            $this->settledCount = $service->settleEligibleMembers($case, function ($current, $total, $msg) {
                $this->progressMessage = $msg;
                $this->progressPercentage = 10 + (int)(($current / max($total, 1)) * 85);
            });
        } else {
            $this->activeStep = 2;
            $this->progressPercentage = 100;
            $this->progressMessage = "Auto-settlement skipped as requested.";
        }

        $this->progressPercentage = 100;
        $this->progressMessage = "Operations completed successfully.";
        usleep(300000);

        // Flash session data for index view
        session()->flash('solidarity_settled', [
            'enabled' => $this->auto_settle,
            'settled' => $this->settledCount,
            'attempted' => User::where('status', 'active')->where('id', '!=', $this->user_id)->count(),
            'case_number' => $case->case_number,
            'member_name' => $memberName,
            'member_no' => $membershipNo,
        ]);

        // Dispatch browser event to trigger SweetAlert dialog before redirecting
        $this->dispatch('case-opened-success', ['redirectUrl' => route('admin.benevolence.cases.index')]);
    }

    public function render()
    {
        $members = collect();
        if (strlen(trim($this->searchMember)) > 0) {
            $members = User::where('first_name', 'like', '%' . $this->searchMember . '%')
                ->orWhere('last_name', 'like', '%' . $this->searchMember . '%')
                ->orWhere('membership_number', 'like', '%' . $this->searchMember . '%')
                ->orWhere('tsc_number', 'like', '%' . $this->searchMember . '%')
                ->limit(5)
                ->get();
        }

        return view('livewire.admin.benevolence-cases.create', [
            'searchedMembers' => $members,
            'categories' => BenevolenceCategory::all(),
        ]);
    }
}