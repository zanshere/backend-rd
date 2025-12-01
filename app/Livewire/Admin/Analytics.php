<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Analytics extends Component
{
    public string $dateRange = 'week';
    public string $totalOrders = '0';
    public float $orderGrowth = 0;
    public string $totalRevenue = 'Rp 0';
    public float $revenueGrowth = 0;
    public string $activeUsers = '0';
    public float $userGrowth = 0;
    public float $completionRate = 0;

    /**
     * @var array<int, array{status: string, count: int, percentage: float}>
     */
    public array $orderStatusDistribution = [];

    /**
     * @var array<int, \App\Models\Package>
     */
    public array $popularPackages = [];

    /**
     * @var array<int, array{icon: string, description: string, time: string}>
     */
    public array $recentActivities = [];

    public function mount()
    {
        $this->loadAnalyticsData();
    }

    public function updatedDateRange()
    {
        $this->loadAnalyticsData();
    }

    private function loadAnalyticsData()
    {
        // Hitung tanggal berdasarkan dateRange
        [$startDate, $endDate] = $this->getDateRange();

        // Data periode sebelumnya untuk perbandingan
        $previousStartDate = $startDate->copy()->subDays($this->getRangeDays());
        $previousEndDate = $startDate->copy()->subDay();

        // 1. Total Orders & Growth
        $currentOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();

        $this->totalOrders = number_format($currentOrders);
        $this->orderGrowth = $previousOrders > 0
            ? round((($currentOrders - $previousOrders) / $previousOrders) * 100, 1)
            : ($currentOrders > 0 ? 100 : 0);

        // 2. Total Revenue & Growth
        $currentRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'accepted'])
            ->sum('total_price');

        $previousRevenue = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->whereIn('status', ['completed', 'accepted'])
            ->sum('total_price');

        $this->totalRevenue = 'Rp ' . number_format($currentRevenue, 0, ',', '.');
        $this->revenueGrowth = $previousRevenue > 0
            ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
            : ($currentRevenue > 0 ? 100 : 0);

        // 3. Active Users & Growth
        $currentUsers = User::whereBetween('last_activity_at', [$startDate, $endDate])
            ->where('is_active', true)
            ->count();

        $previousUsers = User::whereBetween('last_activity_at', [$previousStartDate, $previousEndDate])
            ->where('is_active', true)
            ->count();

        $this->activeUsers = number_format($currentUsers);
        $this->userGrowth = $previousUsers > 0
            ? round((($currentUsers - $previousUsers) / $previousUsers) * 100, 1)
            : ($currentUsers > 0 ? 100 : 0);

        // 4. Completion Rate
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $this->completionRate = $currentOrders > 0
            ? round(($completedOrders / $currentOrders) * 100, 1)
            : 0;

        // 5. Order Status Distribution
        $this->loadOrderStatusDistribution($startDate, $endDate);

        // 6. Popular Packages
        $this->loadPopularPackages($startDate, $endDate);

        // 7. Recent Activities
        $this->loadRecentActivities();
    }

    private function getDateRange()
    {
        $endDate = Carbon::now();

        switch ($this->dateRange) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfWeek();
        }

        return [$startDate, $endDate];
    }

    private function getRangeDays()
    {
        switch ($this->dateRange) {
            case 'today':
            case 'yesterday':
                return 1;
            case 'week':
                return 7;
            case 'month':
                return 30;
            case 'year':
                return 365;
            default:
                return 7;
        }
    }

    private function loadOrderStatusDistribution($startDate, $endDate)
    {
        $statuses = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $total = $statuses->sum('count');

        $this->orderStatusDistribution = $statuses->map(function ($item) use ($total) {
            return [
                'status' => $item->status,
                'count' => $item->count,
                'percentage' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0
            ];
        })->toArray();
    }

    private function loadPopularPackages($startDate, $endDate)
    {
        // Jika menggunakan relasi many-to-many
        $this->popularPackages = Package::withCount(['orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('orders.created_at', [$startDate, $endDate]);
            }])
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get();

        // Jika tidak ada relasi, gunakan query manual
        if ($this->popularPackages->isEmpty()) {
            $this->popularPackages = Package::select('packages.*', DB::raw('COUNT(orders.id) as orders_count'))
                ->leftJoin('orders', 'packages.id', '=', 'orders.package_id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->groupBy('packages.id')
                ->orderBy('orders_count', 'desc')
                ->limit(5)
                ->get();
        }
    }

    private function loadRecentActivities()
    {
        // Coba ambil dari ActivityLog jika ada
        if (class_exists('App\Models\ActivityLog')) {
            $activities = ActivityLog::with('user')
                ->latest()
                ->limit(10)
                ->get();

            $this->recentActivities = $activities->map(function ($activity) {
                return [
                    'icon' => $this->getActivityIcon($activity->type ?? 'activity'),
                    'description' => $activity->description ?? 'Aktivitas',
                    'time' => $activity->created_at->diffForHumans()
                ];
            })->toArray();
        } else {
            // Fallback: ambil dari log order terbaru
            $orders = Order::with('user')
                ->latest()
                ->limit(10)
                ->get();

            $this->recentActivities = $orders->map(function ($order) {
                return [
                    'icon' => 'shopping-cart',
                    'description' => 'Pesanan #' . $order->id . ' oleh ' . ($order->user->name ?? 'User'),
                    'time' => $order->created_at->diffForHumans()
                ];
            })->toArray();
        }
    }

    private function getActivityIcon($type)
    {
        $icons = [
            'order_created' => 'shopping-cart',
            'order_updated' => 'edit',
            'order_completed' => 'check-circle',
            'user_registered' => 'user-plus',
            'payment_received' => 'dollar-sign',
            'system_notification' => 'bell',
        ];

        return $icons[$type] ?? 'activity';
    }

    public function render()
    {
        return view('livewire.admin.analytics')
            ->layout('layouts.app');
    }
}
