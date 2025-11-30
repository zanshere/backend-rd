<?php
// app/Livewire/Help.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Help extends Component
{
    public function render()
    {
        return view('livewire.help', [
            'title' => __('Bantuan')
        ]);
    }
}
