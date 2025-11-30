<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Feedback;
use App\Models\Package;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $recentOrders = [];
    public $recentUsers = [];
    public $orderStatusChart = [];

    /**
     * Initialize component data
     */
    public function mount(): void
    {
        $this->loadStats();
        $this->loadRecentOrders();
        $this->loadRecentUsers();
        $this->loadOrderStatusChart();
    }

    /**
     * Load admin statistics
     */
    private function loadStats(): void
    {
        $this->stats = [
            'total_users' => User::where('role', User::ROLE_USER)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'revenue' => Order::where('status', Order::STATUS_COMPLETED)->sum('total_price'),
            'new_feedbacks' => Feedback::where('status', Feedback::STATUS_PENDING)->count(),
        ];
    }

    /**
     * Load recent orders for admin
     */
    private function loadRecentOrders(): void
    {
        $this->recentOrders = Order::with(['user', 'package'])
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * Load recent registered users
     */
    private function loadRecentUsers(): void
    {
        $this->recentUsers = User::where('role', User::ROLE_USER)
            ->withCount('orders')
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * Load data for order status chart
     */
    private function loadOrderStatusChart(): void
    {
        $this->orderStatusChart = [
            'pending' => Order::where('status', Order::STATUS_PENDING)->count(),
            'confirmed' => Order::where('status', Order::STATUS_CONFIRMED)->count(),
            'in_progress' => Order::where('status', Order::STATUS_IN_PROGRESS)->count(),
            'completed' => Order::where('status', Order::STATUS_COMPLETED)->count(),
            'cancelled' => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'draft' => Order::where('status', Order::STATUS_DRAFT)->count(),
        ];
    }

    /**
     * Get status display name for chart
     */
    public function getStatusDisplayName($status): string
    {
        return match($status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'in_progress' => 'Dalam Pengerjaan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'draft' => 'Draft',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get status badge color for chart
     */
    public function getStatusBadgeColor($status): string
    {
        return match($status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'in_progress' => 'indigo',
            'completed' => 'green',
            'cancelled' => 'red',
            'draft' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get revenue growth percentage (example calculation)
     */
    public function getRevenueGrowthProperty(): float
    {
        // This is a simplified example - you might want to calculate actual growth
        $currentMonthRevenue = Order::where('status', Order::STATUS_COMPLETED)
            ->whereMonth('created_at', now()->month)
            ->sum('total_price');

        $previousMonthRevenue = Order::where('status', Order::STATUS_COMPLETED)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_price');

        if ($previousMonthRevenue == 0) {
            return $currentMonthRevenue > 0 ? 100 : 0;
        }

        return (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100;
    }

    /**
     * Get order growth percentage
     */
    public function getOrderGrowthProperty(): float
    {
        $currentMonthOrders = Order::whereMonth('created_at', now()->month)->count();
        $previousMonthOrders = Order::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($previousMonthOrders == 0) {
            return $currentMonthOrders > 0 ? 100 : 0;
        }

        return (($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders) * 100;
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
