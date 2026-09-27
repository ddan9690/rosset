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
    
    // For date combination conversion
    public $deadline_date; 

    public function edit($field)
    {
        $this->editingField = $field;
        $settings = Setting::first();

        if ($field === 'deadline') {
            // Combine month and day into YYYY-MM-DD for date input (defaulting to current year 2026)
            if ($settings && $settings->registration_deadline_month && $settings->registration_deadline_day) {
                $year = date('Y');
                $month = str_pad($settings->registration_deadline_month, 2, '0', STR_PAD_LEFT);
                $day = str_pad($settings->registration_deadline_day, 2, '0', STR_PAD_LEFT);
                $this->deadline_date = "{$year}-{$month}-{$day}";
            } else {
                $this->deadline_date = null;
            }
        } else {
            $this->registration_fee = $settings ? $settings->registration_fee : null;
            $this->agm_contribution_fee = $settings ? $settings->agm_contribution_fee : null;
            $this->late_registration_waiting_period_days = $settings ? $settings->late_registration_waiting_period_days : null;
        }
        
        $this->resetErrorBag();
    }

    public function cancelEdit()
    {
        $this->editingField = null;
        $this->resetErrorBag();
    }

    public function updateSetting($field)
    {
        if ($field === 'deadline') {
            $this->validate([
                'deadline_date' => 'required|date'
            ]);

            $timestamp = strtotime($this->deadline_date);
            $month = date('n', $timestamp);
            $day = date('j', $timestamp);

            Setting::updateOrCreate(
                ['id' => 1],
                [
                    'registration_deadline_month' => $month,
                    'registration_deadline_day' => $day,
                ]
            );
        } else {
            $rules = [
                'registration_fee' => 'required|integer|min:0',
                'agm_contribution_fee' => 'required|integer|min:0',
                'late_registration_waiting_period_days' => 'required|integer|min:0',
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