<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('ROSSET-SWA Constitution')]
class Constitution extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.constitution');
    }
}