<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
use App\Models\BenevolenceCategory;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Edit Benevolence Case | Admin ROSSET-SWA')]
class Edit extends Component
{
    public $caseId;
    public $case;

    public $user_id;
    public $benevolence_category_id;
    public $case_details;
    public $deadline;
    public $status;

    public function mount($id, $slug)
    {
        $this->case = BenevolenceCase::where('id', $id)->where('slug', $slug)->firstOrFail();
        
        $this->caseId = $this->case->id;
        $this->user_id = $this->case->user_id;
        $this->benevolence_category_id = $this->case->benevolence_category_id;
        $this->case_details = $this->case->case_details;
        $this->deadline = $this->case->deadline;
        $this->status = $this->case->status;
    }

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'benevolence_category_id' => 'required|exists:benevolence_categories,id',
            'case_details' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:active,closed,suspended',
        ];
    }

    public function update()
    {
        $this->validate();

        $this->case->update([
            'user_id' => $this->user_id,
            'benevolence_category_id' => $this->benevolence_category_id,
            'case_details' => $this->case_details,
            'deadline' => $this->deadline,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Benevolence case updated successfully.');
        return redirect()->route('admin.benevolence.cases.index');
    }

    public function render()
    {
        return view('livewire.admin.benevolence-cases.edit', [
            'members' => User::orderBy('first_name')->get(),
            'categories' => BenevolenceCategory::all(),
        ]);
    }
}