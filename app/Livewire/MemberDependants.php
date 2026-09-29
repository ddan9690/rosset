<?php

namespace App\Livewire;

use App\Models\MemberDependant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manage Dependants | ROSSET-SWA')]
class MemberDependants extends Component
{
    // Arrays to hold dynamic dependant entries
    public $spouse = [];
    public $children = [];
    public $parents = [];
    public $siblings = [];

    public function mount()
    {
        $user = Auth::user();
        
        if ($user) {
            $record = MemberDependant::where('user_id', $user->id)->first();
            
            if ($record && !empty($record->dependants)) {
                $this->spouse = $record->dependants['spouse'] ?? [];
                $this->children = $record->dependants['children'] ?? [];
                $this->parents = $record->dependants['parents'] ?? [];
                $this->siblings = $record->dependants['siblings'] ?? [];
            }
        }
    }

    public function addSpouse()
    {
        $this->spouse[] = ['name' => '', 'dob' => ''];
    }

    public function removeSpouse($index)
    {
        unset($this->spouse[$index]);
        $this->spouse = array_values($this->spouse);
    }

    public function addChild()
    {
        $this->children[] = ['name' => '', 'dob' => ''];
    }

    public function removeChild($index)
    {
        unset($this->children[$index]);
        $this->children = array_values($this->children);
    }

    public function addParent()
    {
        $this->parents[] = ['name' => '', 'relation' => 'Parent'];
    }

    public function removeParent($index)
    {
        unset($this->parents[$index]);
        $this->parents = array_values($this->parents);
    }

    public function addSibling()
    {
        $this->siblings[] = ['name' => ''];
    }

    public function removeSibling($index)
    {
        unset($this->siblings[$index]);
        $this->siblings = array_values($this->siblings);
    }

    public function saveDependants()
    {
        $user = Auth::user();

        $this->validate([
            'spouse.*.name' => 'required|string|max:255',
            'spouse.*.dob' => 'nullable|date',
            'children.*.name' => 'required|string|max:255',
            'children.*.dob' => 'nullable|date',
            'parents.*.name' => 'required|string|max:255',
            'parents.*.relation' => 'nullable|string|max:255',
            'siblings.*.name' => 'required|string|max:255',
        ]);

        $payload = [
            'spouse' => $this->spouse,
            'children' => $this->children,
            'parents' => $this->parents,
            'siblings' => $this->siblings,
        ];

        MemberDependant::updateOrCreate(
            ['user_id' => $user->id],
            ['dependants' => $payload]
        );

        session()->flash('message', 'Dependants successfully updated!');

        return redirect()->route('portal');
    }

    public function render()
    {
        return view('livewire.member-dependants');
    }
}