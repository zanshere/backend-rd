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
            'total_users' => User::regular()->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'revenue' => Order::completed()->sum('total_price'),
            'new_feedbacks' => Feedback::pending()->count(),
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
        $this->recentUsers = User::regular()
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
            'pending' => Order::pending()->count(),
            'in_progress' => Order::inProgress()->count(),
            'completed' => Order::completed()->count(),
            'cancelled' => Order::where('status', Order::STATUS_CANCELLED)->count(),
        ];
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.app');
    }
}
