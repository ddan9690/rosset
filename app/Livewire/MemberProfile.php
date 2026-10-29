<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Member Profile | ROSSET-SWA')]
class MemberProfile extends Component
{
    use WithFileUploads;

    public $salutation;
    public $first_name;
    public $last_name;
    public $gender;
    public $membership_number;
    public $tsc_number;
    public $id_number;
    public $joined_at;
    public $status;

    // Editable fields
    public $phone;
    public $email;
    public $school_level;
    public $school;
    public $profile_picture;
    public $existing_profile_picture;

    public function mount()
    {
        $user = Auth::user();

        $this->salutation = $user->salutation;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->gender = $user->gender;
        $this->membership_number = $user->membership_number ?? 'Not Assigned';
        $this->tsc_number = $user->tsc_number;
        $this->id_number = $user->id_number;
        $this->joined_at = $user->joined_at ? $user->joined_at->format('d-M-Y') : 'N/A';
        $this->status = ucfirst($user->status);

        // Populate editable fields
        $this->phone = $user->phone;
        $this->email = $user->email;
        $this->school_level = $user->school_level;
        $this->school = $user->school;
        $this->existing_profile_picture = $user->profile_picture;
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/',
            ],
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
            'school_level' => 'required|string',
            'school' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048', // Max 2MB
        ], [
            'phone.regex' => 'Please enter a valid phone number format (e.g. 0712345678).',
            'email.unique' => 'This email address is already in use by another member.',
        ]);

        $profilePath = $this->existing_profile_picture;

        if ($this->profile_picture) {
            // Delete old picture if it exists
            if ($this->existing_profile_picture && Storage::disk('public')->exists($this->existing_profile_picture)) {
                Storage::disk('public')->delete($this->existing_profile_picture);
            }

            $profilePath = $this->profile_picture->store('profile-pictures', 'public');
            $this->existing_profile_picture = $profilePath;
        }

        $user->update([
            'phone' => $this->phone,
            'email' => $this->email,
            'school_level' => $this->school_level,
            'school' => $this->school,
            'profile_picture' => $profilePath,
        ]);

        // Reset temporary file input property
        $this->profile_picture = null;

        session()->flash('success', 'Profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.member-profile');
    }
}