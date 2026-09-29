<?php

namespace App\Livewire\Admin\Members;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Manage Members | Admin ROSSET-SWA')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteMember($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself if logged in as admin
        if (auth()->id() === $user->id) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user->delete();

        session()->flash('message', 'Member deleted successfully.');
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

        return view('livewire.admin.members.index', [
            'users' => $users,
        ]);
    }
}