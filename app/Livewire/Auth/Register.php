<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Membership Registration | ROSSET-SWA')]
class Register extends Component
{
    public $title = 'Membership Registration | ROSSET-SWA';

    public $step = 1; // 1 = Lookup step, 2 = Full registration form
    public $lookup_input = ''; // TSC number lookup

    // Full form fields
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

    public function checkMember()
    {
        $this->validate([
            'lookup_input' => 'required|string',
        ]);

        $user = User::where('tsc_number', $this->lookup_input)->first();

        // If TSC number is found AND registration fee is paid, take them to the login page
        if ($user && $user->registration_fee_paid) {
            session()->flash('info', 'Your account is already registered and active. Please log in.');
            return redirect()->route('login');
        }

        // If found but registration fee is NOT paid yet, log them in and take them to fee payment
        if ($user && !$user->registration_fee_paid) {
            Auth::login($user);
            $user->update(['last_login_at' => now()]);
            session()->flash('info', 'Please complete your registration fee payment.');
            return redirect()->route('register.fee');
        }

        // If NOT found at all, proceed to Step 2 (Full Registration Form)
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
        ], [
            'phone.regex' => 'Please enter a valid phone number format.',
        ]);

        try {
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
                'status' => 'pending',
                'registration_fee_paid' => false,
                'is_profile_complete' => false,
                'password' => Hash::make($this->password),
                'last_login_at' => now(),
            ]);

            Auth::login($user);

            // Redirect to registration fee payment page
            return redirect()->route('register.fee');
        } catch (\Exception $e) {
            $this->addError('email', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}