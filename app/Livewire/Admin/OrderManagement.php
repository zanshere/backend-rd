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
                  ->orWhere('description', 'like', "%{$this->search}%")
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
            Order::STATUS_ACCEPTED,
            Order::STATUS_REJECTED,
            Order::STATUS_IN_PROGRESS,
            Order::STATUS_COMPLETED,
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
     * Get status options for filter
     */
    public function getStatusOptionsProperty(): array
    {
        return [
            'all' => 'Semua Status',
            Order::STATUS_PENDING => 'Pending',
            Order::STATUS_ACCEPTED => 'Diterima',
            Order::STATUS_IN_PROGRESS => 'Dalam Progress',
            Order::STATUS_REVISION => 'Revisi',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
            Order::STATUS_REJECTED => 'Ditolak',
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
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.order-management');
    }
}
