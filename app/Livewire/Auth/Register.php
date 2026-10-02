<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\MembershipRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.auth')]
#[Title('Membership Registration | ROSSET-SWA')]
class Register extends Component
{
    use WithFileUploads;

    public $title = 'Membership Registration | ROSSET-SWA';

    public $step = 1; 
    public $lookup_input = ''; 

    public $first_name = '';
    public $last_name = '';
    public $salutation = 'Mr.';
    public $gender = 'male';
    public $phone = '';
    public $tsc_number = '';
    public $id_number = '';
    public $school_level = 'Primary';
    public $school = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $profile_picture; // Added for file upload

    /**
     * Redirect already authenticated users away from the registration page.
     */
    public function mount()
    {
        if (Auth::check()) {
            return redirect()->intended(route('portal'));
        }
    }

    public function checkMember()
    {
        $this->validate([
            'lookup_input' => 'required|string',
        ]);

        $user = User::where('tsc_number', $this->lookup_input)->first();

        if ($user && $user->registration_fee_paid) {
            session()->flash('info', 'Your account is already registered and active. Please log in.');
            return redirect()->route('login');
        }

        if ($user && !$user->registration_fee_paid) {
            Auth::login($user);
            $user->update(['last_login_at' => now()]);
            session()->flash('info', 'Please complete your registration fee payment.');
            return redirect()->route('register.fee');
        }

        $this->tsc_number = $this->lookup_input;
        $this->step = 2;
    }

    public function register()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'salutation' => 'required|in:Mr.,Mrs.',
            'gender' => 'required|in:male,female',
            'phone' => ['required', 'string', 'regex:/^(?:254[17]\d{8}|0[17]\d{8}|[17]\d{8})$/'],
            'tsc_number' => 'required|string|unique:users,tsc_number',
            'id_number' => 'required|string|unique:users,id_number',
            'school_level' => 'required|string',
            'school' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'profile_picture' => 'nullable|image|max:2048', // Max 2MB image validation
        ], [
            'phone.regex' => 'Please enter a valid phone number format.',
        ]);

        try {
            DB::transaction(function () {
                $profilePath = null;
                if ($this->profile_picture) {
                    $profilePath = $this->profile_picture->store('profile-pictures', 'public');
                }

                $user = User::create([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'salutation' => $this->salutation,
                    'gender' => $this->gender,
                    'phone' => $this->phone,
                    'tsc_number' => $this->tsc_number,
                    'id_number' => $this->id_number,
                    'school_level' => $this->school_level,
                    'school' => $this->school,
                    'email' => $this->email,
                    'profile_picture' => $profilePath,
                    'status' => 'pending',
                    'registration_fee_paid' => false,
                    'is_profile_complete' => false,
                    'password' => Hash::make($this->password),
                ]);

                // Automatically assign the default 'member' role
                $user->assignRole('member');

                MembershipRequest::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'approved_by' => null,
                ]);

                Auth::login($user);
            });

            return redirect()->route('membership.status');
        } catch (\Exception $e) {
            $this->addError('email', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}