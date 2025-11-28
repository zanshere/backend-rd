<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                    Dashboard Admin 🎯
                </h1>
                <p class="text-zinc-600 dark:text-zinc-400 mt-2">
                    Kelola semua aktivitas sistem website service.
                </p>
            </div>
            <div class="flex gap-3 mt-4 md:mt-0">
                <a
                    href="{{ route('admin.orders') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
                >
                    <i data-lucide="package" class="w-4 h-4"></i>
                    Kelola Pesanan
                </a>
                <a
                    href="{{ route('admin.users') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-600 hover:bg-zinc-700 text-white rounded-lg transition-colors duration-200"
                >
                    <i data-lucide="users" class="w-4 h-4"></i>
                    Kelola User
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Users -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-4">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total User</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-4">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pesanan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 mr-4">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pending</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['pending_orders'] }}</p>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mr-4">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pendapatan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        Rp {{ number_format($stats['revenue'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- New Feedbacks -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 mr-4">
                    <i data-lucide="message-square" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Feedback Baru</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['new_feedbacks'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="border-b border-zinc-200 dark:border-zinc-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        Pesanan Terbaru
                    </h3>
                    <a
                        href="{{ route('admin.orders') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center gap-1"
                    >
                        Kelola Semua
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentOrders->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-zinc-900 dark:text-white">{{ $order->package->name }}</h4>
                                            <p class="text-sm text-zinc-500 dark:text-zinc-400">#{{ $order->order_number }}</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Oleh: {{ $order->user->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @elseif($order->status === 'progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white mt-1">{{ $order->display_total_price }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="package" class="w-12 h-12 text-zinc-400 mx-auto mb-4"></i>
                        <p class="text-zinc-500 dark:text-zinc-400">Belum ada pesanan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="border-b border-zinc-200 dark:border-zinc-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        User Terbaru
                    </h3>
                    <a
                        href="{{ route('admin.users') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center gap-1"
                    >
                        Kelola Semua
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentUsers->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentUsers as $user)
                            <div class="flex items-center justify-between p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 font-medium">
                                        {{ $user->initials() }}
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</h4>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ $user->orders_count }} pesanan
                                    </span>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $user->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="users" class="w-12 h-12 text-zinc-400 mx-auto mb-4"></i>
                        <p class="text-zinc-500 dark:text-zinc-400">Belum ada user terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Status Chart -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            Statistik Status Pesanan
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($orderStatusChart as $status => $count)
                <div class="text-center p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $count }}</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 capitalize">{{ str_replace('_', ' ', $status) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
