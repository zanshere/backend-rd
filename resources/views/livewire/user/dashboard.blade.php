<div class="space-y-4 sm:space-y-6">
    <!-- Welcome Section -->
    <div class="card-responsive">
        <div class="flex-responsive-between">
            <div class="w-full sm:w-auto">
                <h1 class="text-responsive-lg font-bold text-gray-900 dark:text-white">
                    Selamat datang, {{ Auth::user()->name }}!
                </h1>
                <p class="text-responsive-base text-gray-600 dark:text-gray-400 mt-1 sm:mt-2">
                    Pantau progress pembuatan website Anda di sini.
                </p>
            </div>
            <a
                href="{{ route('landing-page') }}"
                class="btn-responsive mt-4 sm:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 touch-target"
            >
                <i data-lucide="plus" class="icon-responsive-sm"></i>
                <span class="hidden xs:inline">Pesan Website Baru</span>
                <span class="xs:hidden">Pesan Baru</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid-responsive-4 gap-responsive">
        <!-- Total Orders -->
        <div class="card-responsive">
            <div class="flex items-center">
                <div class="p-2 xs:p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-3 sm:mr-4">
                    <i data-lucide="package" class="icon-responsive-md"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Total Pesanan</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="card-responsive">
            <div class="flex items-center">
                <div class="p-2 xs:p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 mr-3 sm:mr-4">
                    <i data-lucide="clock" class="icon-responsive-md"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Dalam Progress</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white">{{ $stats['in_progress'] }}</p>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="card-responsive">
            <div class="flex items-center">
                <div class="p-2 xs:p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-3 sm:mr-4">
                    <i data-lucide="check-circle" class="icon-responsive-md"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Selesai</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="card-responsive">
            <div class="flex items-center">
                <div class="p-2 xs:p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mr-3 sm:mr-4">
                    <i data-lucide="hourglass" class="icon-responsive-md"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Menunggu</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Popular Packages -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Recent Orders -->
        <div class="card-responsive">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <div class="flex-responsive-between">
                    <h3 class="text-responsive-md font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="package" class="icon-responsive-sm"></i>
                        Pesanan Terbaru
                    </h3>
                    <a
                        href="{{ route('user.orders') }}"
                        class="text-responsive-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center gap-1 touch-target"
                    >
                        Lihat Semua
                        <i data-lucide="chevron-right" class="icon-responsive-xs"></i>
                    </a>
                </div>
            </div>
            <div class="space-y-3 sm:space-y-4">
                @if($recentOrders->count() > 0)
                    @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 sm:p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex-1 min-w-0 mr-3">
                                <h4 class="font-medium text-gray-900 dark:text-white truncate">{{ $order->package->name }}</h4>
                                <p class="text-responsive-sm text-gray-500 dark:text-gray-400 truncate">#{{ $order->order_number }}</p>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="badge-responsive inline-flex items-center rounded-full font-medium
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @elseif($order->status === 'progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    @if($order->latestProgress)
                                        <span class="text-responsive-sm text-gray-500 dark:text-gray-400">
                                            {{ $order->latestProgress->progress_percentage }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-semibold text-gray-900 dark:text-white text-responsive-base">{{ $order->display_total_price }}</p>
                                <p class="text-responsive-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-6 sm:py-8">
                        <i data-lucide="package" class="icon-responsive-lg text-gray-400 mx-auto mb-3 sm:mb-4"></i>
                        <p class="text-responsive-base text-gray-500 dark:text-gray-400">Belum ada pesanan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Popular Packages -->
        <div class="card-responsive">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <h3 class="text-responsive-md font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="star" class="icon-responsive-sm"></i>
                    Paket Populer
                </h3>
            </div>
            <div class="space-y-3 sm:space-y-4">
                @foreach($popularPackages as $package)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2 gap-2">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-responsive-base truncate">{{ $package->name }}</h4>
                            <span class="font-bold text-blue-600 dark:text-blue-400 text-responsive-base flex-shrink-0">
                                {{ $package->display_price }}
                            </span>
                        </div>
                        <p class="text-responsive-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $package->description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-500 dark:text-gray-400">
                                <i data-lucide="clock" class="icon-responsive-xs inline mr-1"></i>
                                {{ $package->delivery_time }} hari
                            </span>
                            <a
                                href="{{ route('landing-page', ['package' => $package->id]) }}"
                                class="text-responsive-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 sm:px-3 sm:py-1 rounded transition-colors touch-target inline-flex items-center"
                            >
                                <span class="hidden xs:inline">Pesan Sekarang</span>
                                <span class="xs:hidden">Pesan</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions for Mobile -->
    <div class="fixed-bottom-nav show-on-mobile">
        <div class="flex justify-around items-center py-3">
            <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center text-blue-600 dark:text-blue-400">
                <i data-lucide="home" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Dashboard</span>
            </a>
            <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="package" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Pesanan</span>
            </a>
            <a href="{{ route('landing-page') }}" class="flex flex-col items-center text-green-600 dark:text-green-400">
                <div class="bg-green-600 text-white p-3 rounded-full -mt-6 shadow-lg">
                    <i data-lucide="plus" class="icon-responsive-md"></i>
                </div>
                <span class="text-xs mt-2">Pesan</span>
            </a>
            <a href="{{ route('user.history') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="history" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Riwayat</span>
            </a>
            <a href="{{ route('user.feedback') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="message-square" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Feedback</span>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        // Initialize Lucide icons
        if (window.Lucide) {
            window.Lucide.createIcons();
        }

        // Handle mobile bottom nav active state
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.fixed-bottom-nav a');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (currentPath === href || currentPath.startsWith(href + '/')) {
                link.classList.remove('text-gray-600', 'dark:text-gray-400');
                link.classList.add('text-blue-600', 'dark:text-blue-400');
            }
        });

        // Add touch feedback for mobile buttons
        document.querySelectorAll('.touch-target').forEach(button => {
            button.addEventListener('touchstart', function() {
                this.classList.add('opacity-75');
            });

            button.addEventListener('touchend', function() {
                this.classList.remove('opacity-75');
            });
        });
    });

    // Re-initialize icons on Livewire updates
    document.addEventListener('livewire:update', function() {
        if (window.Lucide) {
            window.Lucide.createIcons();
        }
    });
</script>
