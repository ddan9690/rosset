<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class Updates extends Component
{
    public function render()
    {
        return view('livewire.frontend.updates')
            ->layout('layouts.app', [
                'title' => 'Updates, Welfare Cases & Announcements | ROSSET-SWA',
                'metaDescription' => 'Stay informed with official announcements, active bereavement welfare cases, and upcoming community events for teachers across Rongo Sub County and Migori County.'
            ]);
    }
}