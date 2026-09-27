<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceCategory;
use App\Models\User;
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

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'benevolence_category_id' => 'required|exists:benevolence_categories,id',
            'case_details' => 'required|string',
            'deadline' => 'required|date',
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
    }

    public function save()
    {
        $this->validate();

        // 1. Get membership number (fall back to ID if membership_number is missing)
        $membershipNo = $this->selectedMember->membership_number ?? $this->selectedMember->id;

        // 2. Get global incremental sequence across all cases in the system
        $latestCase = BenevolenceCase::latest('id')->first();
        $globalSequence = $latestCase ? $latestCase->id + 1 : 1;

        // 3. Format case number as MembershipNumber/GlobalSequence (e.g., "76/5", "85/6")
        $caseNumber = $membershipNo . '/' . $globalSequence;

        // 4. Generate slug using member's name, membership number, and global sequence (e.g., "john-doe-76-5")
        $memberName = trim(($this->selectedMember->first_name ?? 'member') . ' ' . ($this->selectedMember->last_name ?? ''));
        $slug = Str::slug($memberName . '-' . $membershipNo . '-' . $globalSequence);

        BenevolenceCase::create([
            'case_number' => $caseNumber,
            'slug' => $slug,
            'user_id' => $this->user_id,
            'benevolence_category_id' => $this->benevolence_category_id,
            'case_details' => $this->case_details,
            'deadline' => $this->deadline,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        session()->flash('message', 'Benevolence case created successfully.');
        return redirect()->route('admin.benevolence.cases.index');
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