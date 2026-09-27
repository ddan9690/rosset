<?php

namespace App\Livewire\Admin;

use App\Models\BenevolenceCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Benevolence Categories | Admin ROSSET-SWA')]
class BenevolenceCategories extends Component
{
    use WithPagination;

    public $name = '';
    public $amount = '';
    public $categoryId = null;

    public $showModal = false;
    public $isEditing = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:benevolence_categories,name,' . $this->categoryId,
            'amount' => 'required|integer|min:0',
        ];
    }

    protected $messages = [
        'amount.integer' => 'The amount must be a whole number with no decimals.',
        'amount.min' => 'The amount cannot be negative.',
    ];

    public function openCreateModal()
    {
        $this->reset(['name', 'amount', 'categoryId', 'isEditing']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function editCategory($id)
    {
        $category = BenevolenceCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->amount = $category->amount;
        $this->isEditing = true;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'amount', 'categoryId', 'isEditing']);
    }

    public function saveCategory()
    {
        $this->validate();

        if ($this->isEditing) {
            $category = BenevolenceCategory::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'amount' => (int) $this->amount,
            ]);
            session()->flash('message', 'Benevolence category updated successfully.');
        } else {
            BenevolenceCategory::create([
                'name' => $this->name,
                'amount' => (int) $this->amount,
            ]);
            session()->flash('message', 'Benevolence category created successfully.');
        }

        $this->closeModal();
    }

    public function deleteCategory($id)
    {
        $category = BenevolenceCategory::findOrFail($id);
        $category->delete();

        session()->flash('message', 'Benevolence category deleted successfully.');
    }

    public function render()
    {
        $categories = BenevolenceCategory::latest()->paginate(10);

        return view('livewire.admin.benevolence-categories', [
            'categories' => $categories,
        ]);
    }
}