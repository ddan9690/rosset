<?php

namespace App\Livewire;

use App\Models\Dependent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manage Dependents')]
class Dependants extends Component
{
    public $spouses = [];
    public $parents = [];
    public $children = [];

    // UI toggle states for input forms
    public $showSpouseInput = false;
    public $showParentInput = false;
    public $showChildInput = false;

    // Temporary input fields
    public $spouseName = '';
    public $parentName = '';
    public $childName = '';

    public function mount()
    {
        $record = Dependent::where('user_id', auth()->id())->first();

        if ($record) {
            // Handle backward compatibility if spouse was previously a single array/string
            $savedSpouse = $record->spouse;
            if ($savedSpouse && isset($savedSpouse['name'])) {
                $this->spouses = [$savedSpouse];
            } else {
                $this->spouses = $savedSpouse ?? [];
            }

            $this->parents = $record->parents ?? [];
            $this->children = $record->children ?? [];
        }
    }

    public function saveSpouse()
    {
        $this->validate([
            'spouseName' => 'required|string|max:255',
        ]);

        $this->spouses[] = [
            'name' => trim($this->spouseName),
        ];

        $this->spouseName = '';
        $this->showSpouseInput = false;
        
        $this->persistToDatabase();
    }

    public function removeSpouse($index)
    {
        unset($this->spouses[$index]);
        $this->spouses = array_values($this->spouses);
        $this->persistToDatabase();
    }

    public function saveParent()
    {
        if (count($this->parents) >= 2) {
            session()->flash('error', 'You can only add a maximum of 2 parents.');
            return;
        }

        $this->validate([
            'parentName' => 'required|string|max:255',
        ]);

        $this->parents[] = [
            'name' => trim($this->parentName),
        ];

        $this->parentName = '';
        $this->showParentInput = false;

        $this->persistToDatabase();
    }

    public function removeParent($index)
    {
        unset($this->parents[$index]);
        $this->parents = array_values($this->parents);
        $this->persistToDatabase();
    }

    public function saveChild()
    {
        $this->validate([
            'childName' => 'required|string|max:255',
        ]);

        $this->children[] = [
            'name' => trim($this->childName),
        ];

        $this->childName = '';
        $this->showChildInput = false;

        $this->persistToDatabase();
    }

    public function removeChild($index)
    {
        unset($this->children[$index]);
        $this->children = array_values($this->children);
        $this->persistToDatabase();
    }

    private function persistToDatabase()
    {
        Dependent::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'spouse' => $this->spouses, // stored as JSON array
                'parents' => $this->parents,
                'children' => $this->children,
            ]
        );

        session()->flash('success', 'Dependents updated successfully.');
    }

    public function render()
    {
        return view('livewire.dependants');
    }
}