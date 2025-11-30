<?php

namespace App\Livewire\User;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderDetail extends Component
{
    public $orderId;
    public $order;

    public function mount($order)
    {
        $this->orderId = $order;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = Auth::user()
            ->orders()
            ->with(['package', 'progressUpdates', 'files'])
            ->findOrFail($this->orderId);
    }

    /**
     * Get progress steps based on order status
     */
    public function getProgressStepsProperty(): array
    {
        $steps = [
            [
                'name' => 'Pembayaran',
                'status' => 'completed',
                'description' => 'Menunggu konfirmasi pembayaran',
                'date' => $this->order->created_at->format('d M Y'),
                'completed' => true
            ],
            [
                'name' => 'Konfirmasi',
                'status' => $this->order->status !== Order::STATUS_PENDING ? 'completed' : 'current',
                'description' => 'Pesanan dikonfirmasi admin',
                'date' => $this->order->status !== Order::STATUS_PENDING ? $this->order->updated_at->format('d M Y') : null,
                'completed' => $this->order->status !== Order::STATUS_PENDING
            ],
            [
                'name' => 'Pengerjaan',
                'status' => $this->order->isInProgress() ? 'current' :
                           ($this->order->isCompleted() ? 'completed' : 'upcoming'),
                'description' => 'Tim kami sedang mengerjakan proyek Anda',
                'date' => $this->order->isInProgress() || $this->order->isCompleted() ? $this->order->updated_at->format('d M Y') : null,
                'completed' => $this->order->isCompleted()
            ],
            [
                'name' => 'Selesai',
                'status' => $this->order->isCompleted() ? 'completed' : 'upcoming',
                'description' => 'Proyek telah selesai',
                'date' => $this->order->isCompleted() ? $this->order->updated_at->format('d M Y') : null,
                'completed' => $this->order->isCompleted()
            ]
        ];

        // Adjust steps based on actual order status
        if ($this->order->isCompleted()) {
            foreach ($steps as &$step) {
                $step['completed'] = true;
                $step['status'] = 'completed';
            }
        } elseif ($this->order->isCancelled()) {
            $steps[] = [
                'name' => 'Dibatalkan',
                'status' => 'cancelled',
                'description' => 'Pesanan telah dibatalkan',
                'date' => $this->order->updated_at->format('d M Y'),
                'completed' => true
            ];
        }

        return $steps;
    }

    /**
     * Get current step index
     */
    public function getCurrentStepIndexProperty(): int
    {
        return match($this->order->status) {
            Order::STATUS_PENDING => 0,
            Order::STATUS_CONFIRMED => 1,
            Order::STATUS_IN_PROGRESS => 2,
            Order::STATUS_COMPLETED => 3,
            Order::STATUS_CANCELLED => 4,
            default => 0,
        };
    }

    /**
     * Cancel the order
     */
    public function cancelOrder()
    {
        // Only allow cancellation for pending orders
        if ($this->order->isPending() && $this->order->isPaymentPending()) {
            $this->order->update([
                'status' => Order::STATUS_CANCELLED
            ]);

            session()->flash('message', 'Pesanan berhasil dibatalkan.');
            $this->loadOrder(); // Reload order data
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->order->status) {
            Order::STATUS_DRAFT => 'gray',
            Order::STATUS_PENDING => 'yellow',
            Order::STATUS_CONFIRMED => 'blue',
            Order::STATUS_IN_PROGRESS => 'indigo',
            Order::STATUS_COMPLETED => 'green',
            Order::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeColor(): string
    {
        return match($this->order->payment_status) {
            Order::PAYMENT_PENDING => 'yellow',
            Order::PAYMENT_PAID => 'green',
            Order::PAYMENT_FAILED => 'red',
            Order::PAYMENT_EXPIRED => 'orange',
            default => 'gray',
        };
    }

    /**
     * Check if order can be cancelled
     */
    public function getCanBeCancelledProperty(): bool
    {
        return $this->order->isPending() && $this->order->isPaymentPending();
    }

    /**
     * Get display status name
     */
    public function getDisplayStatusProperty(): string
    {
        return match($this->order->status) {
            Order::STATUS_DRAFT => 'Draft',
            Order::STATUS_PENDING => 'Menunggu Konfirmasi',
            Order::STATUS_CONFIRMED => 'Dikonfirmasi',
            Order::STATUS_IN_PROGRESS => 'Dalam Pengerjaan',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get display payment status name
     */
    public function getDisplayPaymentStatusProperty(): string
    {
        return match($this->order->payment_status) {
            Order::PAYMENT_PENDING => 'Menunggu Pembayaran',
            Order::PAYMENT_PAID => 'Lunas',
            Order::PAYMENT_FAILED => 'Pembayaran Gagal',
            Order::PAYMENT_EXPIRED => 'Pembayaran Kadaluarsa',
            default => 'Tidak Diketahui',
        };
    }

    public function render()
    {
        return view('livewire.user.order-detail')
            ->layout('components.layouts.app');
    }
}
