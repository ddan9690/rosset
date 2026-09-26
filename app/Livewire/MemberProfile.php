<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Update Profile | ROSSET-SWA')]
class MemberProfile extends Component
{
    use WithFileUploads;

    public $salutation;
    public $first_name;
    public $last_name;
    public $gender;
    public $tsc_number;
    public $id_number;
    public $school_level;
    public $school;
    public $phone;
    public $email;
    public $membership_number;
    public $profile_picture;
    public $existing_profile_picture;

    public function mount()
    {
        $user = Auth::user();
        
        if ($user) {
            $this->salutation = $user->salutation;
            $this->first_name = $user->first_name;
            $this->last_name = $user->last_name;
            $this->gender = $user->gender;
            $this->tsc_number = $user->tsc_number;
            $this->id_number = $user->id_number;
            $this->school_level = $user->school_level;
            $this->school = $user->school;
            $this->phone = $user->phone;
            $this->email = $user->email;
            $this->membership_number = $user->membership_number ?? $user->tsc_number;
            $this->existing_profile_picture = $user->profile_picture ?? null;
        }
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'salutation' => 'nullable|in:Mr,Mrs',
            'gender' => 'nullable|in:male,female',
            'tsc_number' => 'required|string|unique:users,tsc_number,' . $user->id,
            'id_number' => 'required|string|unique:users,id_number,' . $user->id,
            'school_level' => 'nullable|in:Junior School,Senior School',
            'school' => 'required|string|max:255',
            // Safaricom prefixes validation (07, 01, +2547, +2541)
            'phone' => ['required', 'string', 'regex:/^(?:254|\+254|0)?(7[0-9]{8}|1[10]{2}[0-9]{6})$/'],
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|max:2048', // max 2MB
        ], [
            'phone.regex' => 'The phone number must be a valid Safaricom number (e.g. 07XXXXXXXX or 01XXXXXXXX).',
        ]);

        $picturePath = $this->existing_profile_picture;
        if ($this->profile_picture) {
            $picturePath = $this->profile_picture->store('profile-pictures', 'public');
        }

        $user->update([
            'salutation' => $this->salutation,
            'gender' => $this->gender,
            'tsc_number' => $this->tsc_number,
            'id_number' => $this->id_number,
            'school_level' => $this->school_level,
            'school' => $this->school,
            'phone' => $this->phone,
            'email' => $this->email,
            'profile_picture' => $picturePath,
            'is_profile_complete' => true,
        ]);

        session()->flash('message', 'Profile successfully updated!');

        return redirect()->route('portal');
    }

    public function render()
    {
        return view('livewire.member-profile');
    }
}