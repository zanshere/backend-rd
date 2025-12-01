<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentFailed extends Component
{
    public $order;
    public $orderId;

    public function mount($order)
    {
        $this->orderId = $order;
        $this->loadOrder();
    }

    protected function loadOrder()
    {
        $this->order = Order::with('package')
            ->where('id', $this->orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    public function retryPayment()
    {
        return redirect()->route('payment.page', ['order' => $this->orderId]);
    }

    public function render()
    {
        return view('livewire.payment-failed');
    }
}
