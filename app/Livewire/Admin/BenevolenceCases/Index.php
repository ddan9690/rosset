<?php

namespace App\Livewire\Admin\BenevolenceCases;

use App\Models\BenevolenceCase;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Manage Benevolence Cases | Admin ROSSET-SWA')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteCase($id)
    {
        $case = BenevolenceCase::findOrFail($id);
        $case->delete();

        session()->flash('message', 'Benevolence case deleted successfully.');
    }

    public function render()
    {
        $cases = BenevolenceCase::with(['member', 'category', 'creator'])
            ->when($this->search, function ($query) {
                $query->where('case_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('member', function ($q) {
                        $q->where('first_name', 'like', '%' . $this->search . '%')
                          ->orWhere('last_name', 'like', '%' . $this->search . '%')
                          ->orWhere('tsc_number', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.benevolence-cases.index', [
            'cases' => $cases,
        ]);
    }
}