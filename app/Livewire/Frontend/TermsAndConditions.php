<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Terms and Conditions | ROSSET-SWA')]
class TermsAndConditions extends Component
{
    public function render()
    {
        return view('livewire.frontend.terms-and-conditions');
    }
}