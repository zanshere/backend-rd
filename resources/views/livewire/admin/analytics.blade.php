<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Analytics</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Analisis data dan statistik sistem</p>
        </div>

        <!-- Date Range -->
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <select
                wire:model.live="dateRange"
                class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            >
                <option value="today">Hari Ini</option>
                <option value="yesterday">Kemarin</option>
                <option value="week">Minggu Ini</option>
                <option value="month">Bulan Ini</option>
                <option value="year">Tahun Ini</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Orders -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pesanan</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $totalOrders }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        <span class="{{ $orderGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $orderGrowth >= 0 ? '+' : '' }}{{ $orderGrowth }}%
                        </span>
                        dari periode sebelumnya
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $totalRevenue }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        <span class="{{ $revenueGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%
                        </span>
                        dari periode sebelumnya
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">User Aktif</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $activeUsers }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        <span class="{{ $userGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $userGrowth >= 0 ? '+' : '' }}{{ $userGrowth }}%
                        </span>
                        dari periode sebelumnya
                    </p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Completion Rate</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $completionRate }}%</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        Pesanan yang berhasil diselesaikan
                    </p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <i data-lucide="trending-up" class="w-6 h-6 text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Orders Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Trend Pesanan</h3>
            <div class="h-64 flex items-center justify-center">
                <div class="text-center">
                    <i data-lucide="bar-chart" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                    <p class="text-zinc-500 dark:text-zinc-400">Chart akan ditampilkan di sini</p>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Trend Pendapatan</h3>
            <div class="h-64 flex items-center justify-center">
                <div class="text-center">
                    <i data-lucide="line-chart" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                    <p class="text-zinc-500 dark:text-zinc-400">Chart akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Status Distribution -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Distribusi Status Pesanan</h3>
            <div class="space-y-3">
                @foreach($orderStatusDistribution as $status)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full
                                @if($status['status'] === 'pending') bg-yellow-500
                                @elseif($status['status'] === 'accepted') bg-blue-500
                                @elseif($status['status'] === 'progress') bg-blue-500
                                @elseif($status['status'] === 'revision') bg-orange-500
                                @elseif($status['status'] === 'completed') bg-green-500
                                @elseif($status['status'] === 'cancelled') bg-red-500
                                @elseif($status['status'] === 'rejected') bg-red-500
                                @endif"></div>
                            <span class="text-sm text-zinc-700 dark:text-zinc-300 capitalize">{{ $status['status'] }}</span>
                        </div>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $status['count'] }} ({{ $status['percentage'] }}%)
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Popular Packages -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Paket Populer</h3>
            <div class="space-y-3">
                @foreach($popularPackages as $package)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $package->name }}</span>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $package->orders_count }} pesanan
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-3">
                @foreach($recentActivities as $activity)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center mt-1">
                            <i data-lucide="{{ $activity['icon'] }}" class="w-4 h-4 text-zinc-600 dark:text-zinc-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-zinc-900 dark:text-white">{{ $activity['description'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $activity['time'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Export Data</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Export data analytics dalam berbagai format</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-2 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 text-sm">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV
                </button>
                <button class="flex items-center gap-2 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 text-sm">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export PDF
                </button>
            </div>
        </div>
    </div>
</div>
