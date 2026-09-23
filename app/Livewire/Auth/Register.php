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
    public $lookup_input = ''; // Can be TSC number or ID number

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

        // Search by TSC number or ID number
        $user = User::where('tsc_number', $this->lookup_input)
            ->orWhere('id_number', $this->lookup_input)
            ->first();

        if ($user) {
            // Found, but registration fee not paid (Partial / Pre-seeded)
            if (!$user->registration_fee_paid || $user->status === 'pending') {
                Auth::login($user);
                session()->flash('message', 'Your profile exists! Please complete your registration by paying the registration fee.');
                return redirect()->route('activation.pending');
            }

            // Found and already active
            $this->addError('lookup_input', 'An active account already exists with these details. Please log in.');
            return;
        }

        // Not found anywhere: default pre-fill to tsc_number
        // If it looks purely like a numeric ID, you can check length/digits, but defaulting to tsc_number per your rule:
        $this->tsc_number = $this->lookup_input;

        $this->step = 2;
    }

    public function register()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'salutation' => 'required|string',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|max:20',
            'tsc_number' => 'required|string|unique:users,tsc_number',
            'id_number' => 'required|string|unique:users,id_number',
            'school_level' => 'required|string',
            'school' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

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
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        return redirect()->route('activation.pending');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}