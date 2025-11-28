<?php

namespace App\Livewire\User;

use Livewire\Component;

class OrderDetail extends Component
{
    public function render()
    {
        return view('livewire.user.order-detail')
            ->layout('layouts.app');
    }
}
