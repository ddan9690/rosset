<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    public $title = 'Member Login | ROSSET-SWA';

    public $login = '';
    public $password = '';
    public $errorMessage = null;

    protected $rules = [
        'login' => 'required',
        'password' => 'required',
    ];

    public function authenticate()
    {
        $this->reset('errorMessage');
        $this->validate();

        $user = User::where('email', $this->login)
            ->orWhere('phone', $this->login)
            ->orWhere('tsc_number', $this->login)
            ->first();

        if ($user && Hash::check($this->password, $user->password)) {
            Auth::login($user);

            if (!$user->registration_fee_paid || $user->status === 'pending') {
                return redirect()->route('activation.pending');
            }

            return redirect()->intended('/dashboard');
        }

        $this->errorMessage = 'Invalid credentials';
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}