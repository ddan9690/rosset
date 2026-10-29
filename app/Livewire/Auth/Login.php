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

    public $tsc_number = '';
    public $password = '';
    public $errorMessage = null;

    /**
     * Redirect already authenticated users away from the login page.
     */
    public function mount()
    {
        if (Auth::check()) {
            return redirect()->intended(route('portal'));
        }
    }

    protected $rules = [
        'tsc_number' => 'required',
        'password' => 'required',
    ];

    public function authenticate()
    {
        $this->reset('errorMessage');
        $this->validate();

        // Find user strictly by TSC number
        $user = User::where('tsc_number', $this->tsc_number)->first();

        if ($user && Hash::check($this->password, $user->password)) {
            Auth::login($user);

            // Stamp last login time
            $user->update(['last_login_at' => now()]);

            // Redirect members straight to their portal route (respecting intended URL if available)
            return redirect()->intended(route('portal'));
        }

        $this->errorMessage = 'Invalid TSC Number or password.';
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}