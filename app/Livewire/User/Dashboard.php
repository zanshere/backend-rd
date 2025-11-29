<?php

namespace App\Livewire\User;

use App\Models\Order;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $recentOrders = [];
    public $popularPackages = [];

    /**
     * Initialize component data
     */
    public function mount(): void
    {
        $this->loadStats();
        $this->loadRecentOrders();
        $this->loadPopularPackages();
    }

    /**
     * Load user statistics
     */
    private function loadStats(): void
    {
        $user = Auth::user();

        $this->stats = [
            'total_orders' => $user->orders()->count(),
            'in_progress' => $user->inProgressOrders()->count(),
            'completed' => $user->completedOrders()->count(),
            'pending' => $user->pendingOrders()->count(),
        ];
    }

    /**
     * Load recent user orders
     */
    private function loadRecentOrders(): void
    {
        $this->recentOrders = Auth::user()
            ->orders()
            ->with(['package', 'latestProgress'])
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * Load popular packages for ordering
     */
    private function loadPopularPackages(): void
    {
        $this->popularPackages = Package::active()
            ->ordered()
            ->limit(3)
            ->get();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.user.dashboard');
    }
}
