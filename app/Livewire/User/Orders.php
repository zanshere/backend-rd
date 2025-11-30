<?php

namespace App\Livewire\User;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';
    public string $search = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    /**
     * Reset pagination when filters change
     */
    public function updating($property): void
    {
        if (in_array($property, ['statusFilter', 'search'])) {
            $this->resetPage();
        }
    }

    /**
     * Get filtered orders
     */
    public function getOrdersProperty(): LengthAwarePaginator
    {
        $query = Auth::user()
            ->orders()
            ->with(['package', 'latestProgress'])
            ->latest();

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhere('project_name', 'like', "%{$this->search}%")
                  ->orWhere('domain_name', 'like', "%{$this->search}%")
                  ->orWhereHas('package', function ($q) {
                      $q->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        return $query->paginate(10);
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(int $orderId): void
    {
        $order = Auth::user()
            ->orders()
            ->where('status', Order::STATUS_PENDING)
            ->findOrFail($orderId);

        $order->update(['status' => Order::STATUS_CANCELLED]);

        session()->flash('message', 'Order berhasil dibatalkan.');
        $this->dispatch('order-updated');
    }

    /**
     * Get status options for filter
     */
    public function getStatusOptionsProperty(): array
    {
        return [
            'all' => 'Semua Status',
            Order::STATUS_DRAFT => 'Draft',
            Order::STATUS_PENDING => 'Menunggu Konfirmasi',
            Order::STATUS_CONFIRMED => 'Dikonfirmasi',
            Order::STATUS_IN_PROGRESS => 'Dalam Pengerjaan',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(string $status): string
    {
        return match($status) {
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
    public function getPaymentStatusBadgeColor(string $paymentStatus): string
    {
        return match($paymentStatus) {
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
    public function canBeCancelled(Order $order): bool
    {
        return $order->isPending() && $order->isPaymentPending();
    }

    /**
     * Get display status name
     */
    public function getDisplayStatus(string $status): string
    {
        return match($status) {
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
    public function getDisplayPaymentStatus(string $paymentStatus): string
    {
        return match($paymentStatus) {
            Order::PAYMENT_PENDING => 'Menunggu Pembayaran',
            Order::PAYMENT_PAID => 'Lunas',
            Order::PAYMENT_FAILED => 'Pembayaran Gagal',
            Order::PAYMENT_EXPIRED => 'Pembayaran Kadaluarsa',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.user.orders', [
            'orders' => $this->orders,
        ]);
    }
}
