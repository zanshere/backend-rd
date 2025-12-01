<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentPending extends Component
{
    public $order;
    public $orderId;
    public $polling = true;

    protected $listeners = ['refresh' => 'checkStatus'];

    public function mount($order)
    {
        $this->orderId = $order;
        $this->loadOrder();

        // Start polling
        $this->dispatch('start-polling');
    }

    protected function loadOrder()
    {
        $this->order = Order::with('package')
            ->where('id', $this->orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    public function checkStatus()
    {
        $this->loadOrder();

        // Redirect if status changed
        if ($this->order->payment_status === Order::PAYMENT_PAID) {
            return redirect()->route('payment.success', ['order' => $this->orderId]);
        } elseif ($this->order->payment_status === Order::PAYMENT_FAILED) {
            return redirect()->route('payment.failed', ['order' => $this->orderId]);
        }
    }

    public function stopPolling()
    {
        $this->polling = false;
        $this->dispatch('stop-polling');
    }

    public function render()
    {
        return view('livewire.payment-pending');
    }
}
