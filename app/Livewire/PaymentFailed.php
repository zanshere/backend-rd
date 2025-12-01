<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentFailed extends Component
{
    public $order = null;
    public $orderId;
    public $errorMessage = '';

    public function mount($order = null)
    {
        if ($order) {
            $this->orderId = $order;
            $this->loadOrder();
        } else {
            // Jika tidak ada order ID, tampilkan pesan error
            $this->errorMessage = 'Data pesanan tidak ditemukan.';
        }
    }

    protected function loadOrder()
    {
        try {
            $this->order = Order::with('package')
                ->where('id', $this->orderId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        } catch (\Exception $e) {
            $this->errorMessage = 'Pesanan tidak ditemukan atau tidak dapat diakses.';
        }
    }

    public function retryPayment()
    {
        if ($this->order) {
            return redirect()->route('payment.page', ['order' => $this->orderId]);
        } else {
            return redirect()->route('user.dashboard')
                ->with('error', 'Tidak dapat mengulang pembayaran karena pesanan tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.payment-failed');
    }
}
