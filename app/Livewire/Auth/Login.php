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

        // Find user by phone, email, or tsc_number
        $user = User::where('phone', $this->login)
            ->orWhere('email', $this->login)
            ->orWhere('tsc_number', $this->login)
            ->first();

        if ($user && Hash::check($this->password, $user->password)) {
            Auth::login($user);

            // Stamp last login time
            $user->update(['last_login_at' => now()]);

            // Redirect members straight to their portal route
            return redirect()->route('portal');
        }

        $this->errorMessage = 'Invalid phone number or password.';
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}