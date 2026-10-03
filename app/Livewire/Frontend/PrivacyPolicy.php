<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Privacy Policy | ROSSET-SWA')]
class PrivacyPolicy extends Component
{
    public function render()
    {
        return view('livewire.frontend.privacy-policy');
    }
}