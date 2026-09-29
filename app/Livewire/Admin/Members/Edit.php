<?php

namespace App\Livewire\Admin\Members;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Edit Member | Admin ROSSET-SWA')]
class Edit extends Component
{
    public User $user;

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
    public $status;
    public $registration_fee_paid;
    public $password;

    public function mount($id)
    {
        $this->user = User::findOrFail($id);

        $this->membership_number = $this->user->membership_number;
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->id_number = $this->user->id_number;
        $this->tsc_number = $this->user->tsc_number;
        $this->gender = $this->user->gender;
        $this->school = $this->user->school;
        $this->school_level = $this->user->school_level;
        $this->status = $this->user->status;
        $this->registration_fee_paid = (bool) $this->user->registration_fee_paid;
    }

    protected function rules()
    {
        return [
            'membership_number' => 'nullable|string|unique:users,membership_number,' . $this->user->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $this->user->id,
            'id_number' => 'nullable|string|unique:users,id_number,' . $this->user->id,
            'tsc_number' => 'nullable|string|unique:users,tsc_number,' . $this->user->id,
            'gender' => 'required|in:male,female,other',
            'school' => 'nullable|string|max:255',
            'school_level' => 'nullable|string|max:100',
            'status' => 'required|in:active,pending,inactive',
            'registration_fee_paid' => 'boolean',
            'password' => 'nullable|string|min:6',
        ];
    }

    public function update()
    {
        $validatedData = $this->validate();

        if (!empty($this->password)) {
            $validatedData['password'] = Hash::make($this->password);
        } else {
            unset($validatedData['password']);
        }

        $this->user->update($validatedData);

        session()->flash('message', 'Member updated successfully.');

        return redirect()->route('admin.members');
    }

    public function render()
    {
        return view('livewire.admin.members.edit');
    }
}