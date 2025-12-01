<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderProgress;
use Illuminate\Support\Facades\Auth;

class OrderDetail extends Component
{
    public $order;
    public $orderId;
    public $progressUpdates = [];
    public $isPolling = false;
    public $lastUpdate = null;

    protected $listeners = ['refreshProgress' => 'loadProgress'];

    public function mount($order)
    {
        $this->orderId = $order;
        $this->loadOrder();
        $this->loadProgress();

        // Start polling for progress updates if order is in progress
        if ($this->order->isInProgress() || $this->order->isConfirmed()) {
            $this->startPolling();
        }
    }

    protected function loadOrder()
    {
        $this->order = Order::with(['package', 'progressUpdates' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->where('id', $this->orderId)
        ->where('user_id', Auth::id())
        ->firstOrFail();
    }

    protected function loadProgress()
    {
        $this->progressUpdates = $this->order->progressUpdates;
        $this->lastUpdate = now()->timestamp;
    }

    public function startPolling()
    {
        $this->isPolling = true;
    }

    public function stopPolling()
    {
        $this->isPolling = false;
    }

    public function checkForUpdates()
    {
        $this->loadOrder();
        $this->loadProgress();

        // Dispatch event jika ada update baru
        if ($this->progressUpdates->count() > 0) {
            $this->dispatch('new-progress-update');
        }
    }

    public function cancelOrder()
    {
        if (!$this->order->canBeCancelled()) {
            $this->dispatch('error', ['message' => 'Pesanan tidak dapat dibatalkan.']);
            return;
        }

        try {
            $this->order->update([
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => Order::PAYMENT_FAILED,
                'admin_notes' => 'Dibatalkan oleh pengguna'
            ]);

            $this->dispatch('success', ['message' => 'Pesanan berhasil dibatalkan.']);
            $this->stopPolling();
            $this->loadOrder();

        } catch (\Exception $e) {
            $this->dispatch('error', ['message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.user.order-detail');
    }
}
