<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Manage System Roles | Admin ROSSET-SWA')]
class Roles extends Component
{
    public $searchSuperAdmin = '';
    public $showSuperAdminSearch = false;

    public $searchWelfareAdmin = '';
    public $showWelfareAdminSearch = false;

    public function toggleSuperAdminSearch()
    {
        $this->showSuperAdminSearch = !$this->showSuperAdminSearch;
        $this->searchSuperAdmin = '';
    }

    public function toggleWelfareAdminSearch()
    {
        $this->showWelfareAdminSearch = !$this->showWelfareAdminSearch;
        $this->searchWelfareAdmin = '';
    }

    public function addRole($userId, $role)
    {
        $user = User::find($userId);
        if ($user) {
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
                session()->flash('message', "Successfully assigned role to {$user->first_name} {$user->last_name}.");
            }
        }

        $this->searchSuperAdmin = '';
        $this->showSuperAdminSearch = false;
        $this->searchWelfareAdmin = '';
        $this->showWelfareAdminSearch = false;
    }

    public function removeRole($userId, $role)
    {
        $user = User::find($userId);
        if ($user) {
            // Prevent removing your own super admin role while logged in
            if ($role === 'super admin' && Auth::id() === $user->id) {
                session()->flash('error', 'You cannot remove super admin rights from yourself.');
                return;
            }

            if ($user->hasRole($role)) {
                $user->removeRole($role);
                session()->flash('message', "Successfully removed role from {$user->first_name} {$user->last_name}.");
            }
        }
    }

    public function render()
    {
        // Fetches users with 'super admin' role
        $superAdmins = User::role('super admin')->get();

        // Fetches users with 'welfare admin' role
        $welfareAdmins = User::role('welfare admin')->get();

        $searchedSuperUsers = collect();
        if (strlen(trim($this->searchSuperAdmin)) > 0) {
            $searchedSuperUsers = User::where('first_name', 'like', '%' . $this->searchSuperAdmin . '%')
                ->orWhere('last_name', 'like', '%' . $this->searchSuperAdmin . '%')
                ->orWhere('membership_number', 'like', '%' . $this->searchSuperAdmin . '%')
                ->orWhere('tsc_number', 'like', '%' . $this->searchSuperAdmin . '%')
                ->limit(5)
                ->get();
        }

        $searchedWelfareUsers = collect();
        if (strlen(trim($this->searchWelfareAdmin)) > 0) {
            $searchedWelfareUsers = User::where('first_name', 'like', '%' . $this->searchWelfareAdmin . '%')
                ->orWhere('last_name', 'like', '%' . $this->searchWelfareAdmin . '%')
                ->orWhere('membership_number', 'like', '%' . $this->searchWelfareAdmin . '%')
                ->orWhere('tsc_number', 'like', '%' . $this->searchWelfareAdmin . '%')
                ->limit(5)
                ->get();
        }

        return view('livewire.admin.roles', [
            'superAdmins' => $superAdmins,
            'welfareAdmins' => $welfareAdmins,
            'searchedSuperUsers' => $searchedSuperUsers,
            'searchedWelfareUsers' => $searchedWelfareUsers,
        ]);
    }
}