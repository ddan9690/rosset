<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class Home extends Component
{
    
    public bool $showWelcomeBanner = true;

    public function dismissBanner()
    {
        $this->showWelcomeBanner = false;
    }

    public function render()
    {
        return view('livewire.frontend.home')
            ->layout('layouts.app', [
                'title' => 'ROSSET-SWA | Rongu Sub County Secondary Teachers Social Welfare Association',
                'metaDescription' => 'Official platform for the Rongu Sub County Secondary Teachers Social Welfare Association. Empowering secondary educators through mutual welfare support, financial transparency via KCB paybill, and educational innovation.'
            ]);
    }
}