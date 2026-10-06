<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('System Settings | ROSSET-SWA')]
class Settings extends Component
{
    public $editingField = null;
    
    // Values for individual updates
    public $registration_fee;
    public $agm_contribution_fee;
    public $late_registration_waiting_period_days;
    public $defaulting_waiting_period_days;
    public $solidarity_max_balance;
    
    // Separated deadline properties
    public $registration_deadline_month;
    public $registration_deadline_day;

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

    public function render()
    {
        return view('livewire.admin.settings', [
            'settings' => Setting::first()
        ]);
    }
}