<?php

namespace App\Livewire\User;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dateFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFilter' => ['except' => 'all'],
    ];

    /**
     * Get filtered orders for history
     */
    public function getOrdersProperty()
    {
        $query = Order::with(['package'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->orderBy('updated_at', 'desc');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhere('custom_package_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('package', function ($packageQuery) {
                      $packageQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply date filter
        $this->applyDateFilter($query);

        return $query->paginate(10);
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query)
    {
        switch ($this->dateFilter) {
            case 'today':
                $query->whereDate('updated_at', today());
                break;
            case 'week':
                $query->where('updated_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('updated_at', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('updated_at', '>=', now()->subYear());
                break;
        }
    }

    /**
     * Get total orders count
     */
    public function getTotalOrdersProperty()
    {
        return Order::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->count();
    }

    /**
     * Get completed orders count
     */
    public function getCompletedOrdersProperty()
    {
        return Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get cancelled orders count
     */
    public function getCancelledOrdersProperty()
    {
        return Order::where('user_id', Auth::id())
            ->whereIn('status', ['cancelled', 'rejected'])
            ->count();
    }

    /**
     * Get total spending
     */
    public function getTotalSpentProperty()
    {
        $total = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->sum('total_price');

        return 'Rp ' . number_format($total, 0, ',', '.');
    }

    /**
     * Download files for completed order
     */
    public function downloadFiles($orderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->findOrFail($orderId);

        // Logic untuk download files
        // Anda bisa implementasi download zip atau redirect ke download page
        session()->flash('message', 'Download functionality will be implemented soon.');
    }

    /**
     * Reorder from existing order
     */
    public function reorder($orderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->findOrFail($orderId);

        // Logic untuk membuat pesanan baru berdasarkan pesanan lama
        // Anda bisa redirect ke order form dengan data dari order ini
        session()->flash('message', 'Reorder functionality will be implemented soon.');
    }

    /**
     * Export history to Excel
     */
    public function exportHistory()
    {
        // Logic untuk export ke Excel
        session()->flash('message', 'Export functionality will be implemented soon.');
    }

    public function render()
    {
        return view('livewire.user.history', [
            'orders' => $this->orders,
            'totalOrders' => $this->totalOrders,
            'completedOrders' => $this->completedOrders,
            'cancelledOrders' => $this->cancelledOrders,
            'totalSpent' => $this->totalSpent,
        ]);
    }
}
