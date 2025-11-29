<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                    Selamat datang, {{ Auth::user()->name }}!
                </h1>
                <p class="text-zinc-600 dark:text-zinc-400 mt-2">
                    Pantau progress pembuatan website Anda di sini.
                </p>
            </div>
            <a
                href="{{ route('landing-page') }}"
                class="mt-4 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
            >
                <i data-lucide="plus" class="w-4 h-4"></i>
                Pesan Website Baru
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Orders -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-4">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pesanan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 mr-4">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Dalam Progress</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['in_progress'] }}</p>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-4">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Selesai</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mr-4">
                    <i data-lucide="hourglass" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Menunggu</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Popular Packages -->
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
                        href="{{ route('user.orders') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center gap-1"
                    >
                        Lihat Semua
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
                                    <h4 class="font-medium text-zinc-900 dark:text-white">{{ $order->package->name }}</h4>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">#{{ $order->order_number }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                            @elseif($order->status === 'progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        @if($order->latestProgress)
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $order->latestProgress->progress_percentage }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $order->display_total_price }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $order->created_at->format('d M Y') }}</p>
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

        <!-- Popular Packages -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="border-b border-zinc-200 dark:border-zinc-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="star" class="w-5 h-5"></i>
                    Paket Populer
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($popularPackages as $package)
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-zinc-900 dark:text-white">{{ $package->name }}</h4>
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ $package->display_price }}
                                </span>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">{{ $package->description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    ⏱️ {{ $package->delivery_time }} hari
                                </span>
                                <a
                                    href="{{ route('landing-page', ['package' => $package->id]) }}"
                                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition-colors"
                                >
                                    Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
