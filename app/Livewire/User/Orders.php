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
                  ->orWhere('description', 'like', "%{$this->search}%")
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
            Order::STATUS_PENDING => 'Pending',
            Order::STATUS_ACCEPTED => 'Diterima',
            Order::STATUS_IN_PROGRESS => 'Dalam Progress',
            Order::STATUS_REVISION => 'Revisi',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.user.orders');
    }
}
