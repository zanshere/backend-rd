<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManagement extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';
    public string $search = '';
    public string $userFilter = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
        'userFilter' => ['except' => ''],
    ];

    /**
     * Reset pagination when filters change
     */
    public function updating($property): void
    {
        if (in_array($property, ['statusFilter', 'search', 'userFilter'])) {
            $this->resetPage();
        }
    }

    /**
     * Get filtered orders for admin
     */
    public function getOrdersProperty(): LengthAwarePaginator
    {
        $query = Order::with(['user', 'package', 'latestProgress'])
            ->latest();

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply user filter
        if (!empty($this->userFilter)) {
            $query->where('user_id', $this->userFilter);
        }

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhere('project_name', 'like', "%{$this->search}%")
                  ->orWhere('domain_name', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function ($q) {
                      $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('package', function ($q) {
                      $q->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        return $query->paginate(15);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);

        $validStatuses = [
            Order::STATUS_CONFIRMED,
            Order::STATUS_IN_PROGRESS,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];

        if (!in_array($status, $validStatuses)) {
            session()->flash('error', 'Status tidak valid.');
            return;
        }

        $order->update(['status' => $status]);

        session()->flash('message', "Status order #{$order->order_number} berhasil diupdate.");
        $this->dispatch('order-status-updated');
    }

    /**
     * Confirm pending order
     */
    public function confirmOrder(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->isPending()) {
            $order->update(['status' => Order::STATUS_CONFIRMED]);
            session()->flash('message', "Order #{$order->order_number} berhasil dikonfirmasi.");
        } else {
            session()->flash('error', "Hanya order dengan status pending yang bisa dikonfirmasi.");
        }
    }

    /**
     * Mark order as in progress
     */
    public function markAsInProgress(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->isConfirmed() || $order->isPending()) {
            $order->update(['status' => Order::STATUS_IN_PROGRESS]);
            session()->flash('message', "Order #{$order->order_number} ditandai sebagai dalam pengerjaan.");
        } else {
            session()->flash('error', "Hanya order dengan status confirmed atau pending yang bisa diproses.");
        }
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->isInProgress()) {
            $order->update(['status' => Order::STATUS_COMPLETED]);
            session()->flash('message', "Order #{$order->order_number} ditandai sebagai selesai.");
        } else {
            session()->flash('error', "Hanya order dengan status in progress yang bisa diselesaikan.");
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if (!$order->isCompleted() && !$order->isCancelled()) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
            session()->flash('message', "Order #{$order->order_number} berhasil dibatalkan.");
        } else {
            session()->flash('error', "Order yang sudah selesai atau dibatalkan tidak bisa dibatalkan lagi.");
        }
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
     * Get users for filter dropdown
     */
    public function getUsersProperty()
    {
        return User::regular()
            ->active()
            ->get(['id', 'name', 'email']);
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
     * Get available actions for order
     */
    public function getAvailableActions(Order $order): array
    {
        $actions = [];

        if ($order->isPending()) {
            $actions[] = [
                'label' => 'Konfirmasi',
                'method' => 'confirmOrder',
                'color' => 'blue',
                'icon' => 'check'
            ];
            $actions[] = [
                'label' => 'Mulai Pengerjaan',
                'method' => 'markAsInProgress',
                'color' => 'indigo',
                'icon' => 'play'
            ];
            $actions[] = [
                'label' => 'Batalkan',
                'method' => 'cancelOrder',
                'color' => 'red',
                'icon' => 'x'
            ];
        } elseif ($order->isConfirmed()) {
            $actions[] = [
                'label' => 'Mulai Pengerjaan',
                'method' => 'markAsInProgress',
                'color' => 'indigo',
                'icon' => 'play'
            ];
            $actions[] = [
                'label' => 'Batalkan',
                'method' => 'cancelOrder',
                'color' => 'red',
                'icon' => 'x'
            ];
        } elseif ($order->isInProgress()) {
            $actions[] = [
                'label' => 'Tandai Selesai',
                'method' => 'markAsCompleted',
                'color' => 'green',
                'icon' => 'check-circle'
            ];
            $actions[] = [
                'label' => 'Batalkan',
                'method' => 'cancelOrder',
                'color' => 'red',
                'icon' => 'x'
            ];
        } elseif ($order->isDraft()) {
            $actions[] = [
                'label' => 'Konfirmasi',
                'method' => 'confirmOrder',
                'color' => 'blue',
                'icon' => 'check'
            ];
            $actions[] = [
                'label' => 'Batalkan',
                'method' => 'cancelOrder',
                'color' => 'red',
                'icon' => 'x'
            ];
        }

        return $actions;
    }

    /**
     * Get order statistics
     */
    public function getOrderStatsProperty(): array
    {
        return [
            'total' => Order::count(),
            'draft' => Order::where('status', Order::STATUS_DRAFT)->count(),
            'pending' => Order::where('status', Order::STATUS_PENDING)->count(),
            'confirmed' => Order::where('status', Order::STATUS_CONFIRMED)->count(),
            'in_progress' => Order::where('status', Order::STATUS_IN_PROGRESS)->count(),
            'completed' => Order::where('status', Order::STATUS_COMPLETED)->count(),
            'cancelled' => Order::where('status', Order::STATUS_CANCELLED)->count(),
        ];
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.order-management', [
            'orders' => $this->orders,
        ]);
    }
}
