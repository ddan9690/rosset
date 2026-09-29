<?php

namespace App\Livewire\Admin\Members;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Add New Member | Admin ROSSET-SWA')]
class Create extends Component
{
    public $membership_number;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $id_number;
    public $tsc_number;
    public $gender;
    public $school;
    public $school_level;
    public $status = 'active';
    public $registration_fee_paid = false;
    public $password;

    public function mount()
    {
        // Default temporary password
        $this->password = 'password123';
    }

    protected function rules()
    {
        return [
            'membership_number' => 'nullable|string|unique:users,membership_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'id_number' => 'nullable|string|unique:users,id_number',
            'tsc_number' => 'nullable|string|unique:users,tsc_number',
            'gender' => 'required|in:male,female,other',
            'school' => 'nullable|string|max:255',
            'school_level' => 'nullable|string|max:100',
            'status' => 'required|in:active,pending,inactive',
            'registration_fee_paid' => 'boolean',
            'password' => 'required|string|min:6',
        ];
    }

    public function save()
    {
        $validatedData = $this->validate();
        $validatedData['password'] = Hash::make($this->password);

        User::create($validatedData);

        session()->flash('message', 'Member created successfully.');

        return redirect()->route('admin.members');
    }

    public function render()
    {
        return view('livewire.admin.members.create');
    }
}