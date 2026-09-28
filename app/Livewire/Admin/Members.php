<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Manage Members | ROSSET-SWA')]
class Members extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $term = '%' . $this->search . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('first_name', 'like', $term)
                      ->orWhere('last_name', 'like', $term)
                      ->orWhere('membership_number', 'like', $term)
                      ->orWhere('tsc_number', 'like', $term)
                      ->orWhere('id_number', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term)
                      ->orWhere('school', 'like', $term);
                });
            })
            ->orderBy('membership_number', 'asc')
            ->paginate(30);

        return view('livewire.admin.members', [
            'users' => $users,
        ]);
    }
}