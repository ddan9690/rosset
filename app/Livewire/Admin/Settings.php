<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('System Settings | ROSSET-SWA')]
class Settings extends Component
{
    public $editingField = null;
    
    // Values for individual settings updates
    public $registration_fee;
    public $agm_contribution_fee;
    public $late_registration_waiting_period_days;
    public $defaulting_waiting_period_days;
    public $max_missed_benevolence_cases;
    public $solidarity_max_balance;
    
    // Separated deadline properties
    public $registration_deadline_month;
    public $registration_deadline_day;

    // Role Management Properties
    public $searchMember = '';
    public $selectedRoleType = 'welfare-admin'; // 'super-admin' or 'welfare-admin'
    public $showRoleModal = false;

    public function edit($field)
    {
        $this->editingField = $field;
        $settings = Setting::first();

        if ($field === 'deadline') {
            $this->registration_deadline_month = $settings?->registration_deadline_month ?? 2;
            $this->registration_deadline_day = $settings?->registration_deadline_day ?? 28;
        } else {
            $this->registration_fee = $settings?->registration_fee;
            $this->agm_contribution_fee = $settings?->agm_contribution_fee;
            $this->late_registration_waiting_period_days = $settings?->late_registration_waiting_period_days;
            $this->defaulting_waiting_period_days = $settings?->defaulting_waiting_period_days;
            $this->max_missed_benevolence_cases = $settings?->max_missed_benevolence_cases ?? 1;
            $this->solidarity_max_balance = $settings?->solidarity_max_balance;
        }
        
        $this->resetErrorBag();
    }

    public function cancelEdit()
    {
        $this->editingField = null;
        $this->resetErrorBag();
    }

    public function getMaxDaysProperty()
    {
        $month = (int) ($this->registration_deadline_month ?? 2);
        return cal_days_in_month(CAL_GREGORIAN, $month, 2024);
    }

    public function updatedRegistrationDeadlineMonth($value)
    {
        $maxDays = cal_days_in_month(CAL_GREGORIAN, (int) $value, 2024);
        if ($this->registration_deadline_day > $maxDays) {
            $this->registration_deadline_day = $maxDays;
        }
    }

    public function updateSetting($field)
    {
        if ($field === 'deadline') {
            $this->validate([
                'registration_deadline_month' => 'required|integer|between:1,12',
                'registration_deadline_day' => 'required|integer|between:1,31',
            ]);

            Setting::updateOrCreate(
                ['id' => 1],
                [
                    'registration_deadline_month' => $this->registration_deadline_month,
                    'registration_deadline_day' => $this->registration_deadline_day,
                ]
            );
        } else {
            $rules = [
                'registration_fee' => 'required|integer|min:0',
                'agm_contribution_fee' => 'required|integer|min:0',
                'late_registration_waiting_period_days' => 'required|integer|min:0',
                'defaulting_waiting_period_days' => 'required|integer|min:0',
                'max_missed_benevolence_cases' => 'required|integer|min:1',
                'solidarity_max_balance' => 'required|integer|min:0',
            ];

            $this->validate([
                $field => $rules[$field] ?? 'required'
            ]);

            Setting::updateOrCreate(
                ['id' => 1],
                [$field => $this->$field]
            );
        }

        $this->editingField = null;
        session()->flash('success', 'System settings updated successfully.');
    }

    // --- Role Management Actions ---

    public function openRoleModal($roleType)
    {
        $user = auth()->user();

        // Allow both Developers and Super Admins to manage Super Admin roles
        if ($roleType === 'super-admin' && !($user->isDeveloper() || $user->hasRole('super-admin'))) {
            session()->flash('error', 'Only Super Admins or Developers can manage Super Admin roles.');
            return;
        }

        $this->selectedRoleType = $roleType;
        $this->searchMember = '';
        $this->showRoleModal = true;
    }

    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->searchMember = '';
    }

    public function assignRoleToUser($userId)
    {
        $authUser = auth()->user();
        $targetUser = User::excludeDevelopers()->findOrFail($userId);

        if ($targetUser->hasRole($this->selectedRoleType)) {
            session()->flash('error', 'This user already possesses this role.');
            return;
        }

        if ($this->selectedRoleType === 'super-admin' && !($authUser->isDeveloper() || $authUser->hasRole('super-admin'))) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        if ($this->selectedRoleType === 'welfare-admin' && !($authUser->isDeveloper() || $authUser->hasRole('super-admin'))) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $targetUser->assignRole($this->selectedRoleType);

        $roleLabel = ucwords(str_replace('-', ' ', $this->selectedRoleType));
        $userName = trim("{$targetUser->first_name} {$targetUser->last_name}");

        $this->closeRoleModal();
        session()->flash('success', "Role '{$roleLabel}' successfully assigned to {$userName}.");
    }

    public function removeRoleFromUser($userId, $roleName)
    {
        $authUser = auth()->user();

        // Prevent self-removal
        if ((int)$userId === (int)$authUser->id) {
            session()->flash('error', 'You cannot remove your own administrative privileges.');
            return;
        }

        $targetUser = User::excludeDevelopers()->findOrFail($userId);

        if ($roleName === 'super-admin' && !($authUser->isDeveloper() || $authUser->hasRole('super-admin'))) {
            session()->flash('error', 'Only Super Admins or Developers can remove Super Admin roles.');
            return;
        }

        $targetUser->removeRole($roleName);

        $userName = trim("{$targetUser->first_name} {$targetUser->last_name}");
        $roleLabel = ucwords(str_replace('-', ' ', $roleName));

        session()->flash('success', "Role '{$roleLabel}' removed from {$userName} successfully.");
    }

    public function render()
    {
        $authUser = auth()->user();
        
        // Fetch non-developer users assigned to super-admin and welfare-admin roles
        $superAdmins = User::excludeDevelopers()->role('super-admin')->get();
        $welfareAdmins = User::excludeDevelopers()->role('welfare-admin')->get();

        // Live search collection for role assignment modal (excluding developers)
        $searchedMembers = collect();
        if (strlen(trim($this->searchMember)) > 0) {
            $searchedMembers = User::excludeDevelopers()
                ->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->searchMember . '%')
                        ->orWhere('last_name', 'like', '%' . $this->searchMember . '%')
                        ->orWhere('membership_number', 'like', '%' . $this->searchMember . '%')
                        ->orWhere('tsc_number', 'like', '%' . $this->searchMember . '%');
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.admin.settings', [
            'settings' => Setting::first(),
            'superAdmins' => $superAdmins,
            'welfareAdmins' => $welfareAdmins,
            'searchedMembers' => $searchedMembers,
            'isDeveloper' => $authUser->isDeveloper(),
            'isSuperAdmin' => $authUser->hasRole('super-admin'),
            'authUserId' => $authUser->id,
        ]);
    }
}