<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('ROSSET-SWA | Rongo Sub County Secondary Teachers Social Welfare Association')]
class Home extends Component
{
    public bool $showWelcomeBanner = true;

    public function dismissBanner()
    {
        $this->showWelcomeBanner = false;
    }

    public function render()
    {
        return view('livewire.frontend.home');
    }
}