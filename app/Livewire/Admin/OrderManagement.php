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

        // Validate status transition
        $validTransitions = $this->getValidStatusTransitions($order->status);

        if (!in_array($status, $validTransitions)) {
            session()->flash('error', 'Status tidak valid atau transisi tidak diizinkan.');
            return;
        }

        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        // Log status change
        $this->logStatusChange($order, $oldStatus, $status);

        // Update payment status if needed
        if ($status === Order::STATUS_CONFIRMED && $order->isPaymentPending()) {
            $order->update(['payment_status' => Order::PAYMENT_PAID]);
        }

        session()->flash('message', "Status order #{$order->order_number} berhasil diupdate dari " .
            $this->getDisplayStatus($oldStatus) . " menjadi " . $this->getDisplayStatus($status) . ".");

        $this->dispatch('order-status-updated');
    }

    /**
     * Get valid status transitions for current status
     */
    private function getValidStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            Order::STATUS_PENDING => [
                Order::STATUS_CONFIRMED,  // menerima pesanan
                Order::STATUS_CANCELLED,  // menolak pesanan
            ],
            Order::STATUS_CONFIRMED => [
                Order::STATUS_IN_PROGRESS,
                Order::STATUS_CANCELLED,
            ],
            Order::STATUS_IN_PROGRESS => [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ],
            Order::STATUS_DRAFT => [
                Order::STATUS_CONFIRMED,
                Order::STATUS_CANCELLED,
            ],
            default => [],
        };
    }

    /**
     * Log status change
     */
    private function logStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        // You can implement logging here if needed
        // For example, create a progress update or log to database
        \App\Models\ProgressUpdate::create([
            'order_id' => $order->id,
            'progress_percentage' => $this->getProgressPercentageForStatus($newStatus),
            'notes' => "Status berubah dari " . $this->getDisplayStatus($oldStatus) .
                      " menjadi " . $this->getDisplayStatus($newStatus),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Get progress percentage for status
     */
    private function getProgressPercentageForStatus(string $status): int
    {
        return match($status) {
            Order::STATUS_DRAFT => 0,
            Order::STATUS_PENDING => 10,
            Order::STATUS_CONFIRMED => 30,
            Order::STATUS_IN_PROGRESS => 60,
            Order::STATUS_COMPLETED => 100,
            Order::STATUS_CANCELLED => 0,
            default => 0,
        };
    }

    /**
     * Confirm pending order (alias for updateOrderStatus)
     */
    public function confirmOrder(int $orderId): void
    {
        $this->updateOrderStatus($orderId, Order::STATUS_CONFIRMED);
    }

    /**
     * Reject pending order (alias for updateOrderStatus)
     */
    public function rejectOrder(int $orderId): void
    {
        $this->updateOrderStatus($orderId, Order::STATUS_CANCELLED);
    }

    /**
     * Mark order as in progress
     */
    public function markAsInProgress(int $orderId): void
    {
        $this->updateOrderStatus($orderId, Order::STATUS_IN_PROGRESS);
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted(int $orderId): void
    {
        $this->updateOrderStatus($orderId, Order::STATUS_COMPLETED);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(int $orderId): void
    {
        $this->updateOrderStatus($orderId, Order::STATUS_CANCELLED);
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
