<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex-responsive-between">
        <div class="w-full">
            <h1 class="text-responsive-lg font-bold text-gray-900 dark:text-white">Riwayat Pesanan</h1>
            <p class="text-responsive-base text-gray-600 dark:text-gray-400 mt-1">Lihat riwayat semua pesanan yang telah selesai</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid-responsive-4 gap-responsive">
        <div class="card-responsive">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Total Pesanan</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="p-2 xs:p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex-shrink-0 ml-3">
                    <i data-lucide="package" class="icon-responsive-md text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="card-responsive">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Selesai</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white mt-1">{{ $completedOrders }}</p>
                </div>
                <div class="p-2 xs:p-3 bg-green-100 dark:bg-green-900/30 rounded-lg flex-shrink-0 ml-3">
                    <i data-lucide="check-circle" class="icon-responsive-md text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <div class="card-responsive">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Dibatalkan</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white mt-1">{{ $cancelledOrders }}</p>
                </div>
                <div class="p-2 xs:p-3 bg-red-100 dark:bg-red-900/30 rounded-lg flex-shrink-0 ml-3">
                    <i data-lucide="x-circle" class="icon-responsive-md text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <div class="card-responsive">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 truncate">Total Pengeluaran</p>
                    <p class="text-responsive-lg font-bold text-gray-900 dark:text-white mt-1">{{ $totalSpent }}</p>
                </div>
                <div class="p-2 xs:p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex-shrink-0 ml-3">
                    <i data-lucide="dollar-sign" class="icon-responsive-md text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-responsive">
        <div class="flex flex-col md:flex-row md:items-center gap-3 sm:gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="sr-only">Cari riwayat pesanan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="icon-responsive-sm text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        id="search"
                        wire:model.live="search"
                        class="form-input-responsive pl-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Cari riwayat pesanan..."
                    />
                </div>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-48 lg:w-64">
                <label for="statusFilter" class="sr-only">Filter status</label>
                <select
                    id="statusFilter"
                    wire:model.live="statusFilter"
                    class="form-select-responsive border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="all">Semua Status</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div class="w-full md:w-48 lg:w-64">
                <label for="dateFilter" class="sr-only">Filter tanggal</label>
                <select
                    id="dateFilter"
                    wire:model.live="dateFilter"
                    class="form-select-responsive border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="all">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Orders List - Mobile View -->
    <div class="show-on-mobile space-y-4">
        @if($orders->count() > 0)
            @foreach($orders as $order)
                <div class="card-responsive">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-responsive-base font-semibold text-gray-900 dark:text-white truncate">
                                #{{ $order->order_number }}
                            </h3>
                            <p class="text-responsive-sm text-gray-500 dark:text-gray-400 mt-1 truncate">
                                {{ $order->package->name }}
                            </p>
                        </div>
                        <span class="badge-responsive inline-flex items-center rounded-full font-medium ml-2 flex-shrink-0
                            @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                            @elseif($order->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                            @endif">
                            @if($order->status === 'completed')
                                <i data-lucide="check-circle" class="icon-responsive-xs mr-1"></i>
                            @else
                                <i data-lucide="x-circle" class="icon-responsive-xs mr-1"></i>
                            @endif
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <!-- Order Details -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <p class="text-responsive-sm text-gray-500 dark:text-gray-400">Tanggal Selesai</p>
                            <p class="text-responsive-sm text-gray-900 dark:text-white font-medium">
                                @if($order->completed_at)
                                    {{ $order->completed_at->format('d M Y') }}
                                @else
                                    {{ $order->updated_at->format('d M Y') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-responsive-sm text-gray-500 dark:text-gray-400">Total</p>
                            <p class="text-responsive-sm text-gray-900 dark:text-white font-medium">
                                {{ $order->display_total_price }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a
                            href="{{ route('user.order-detail', $order) }}"
                            class="flex-1 btn-responsive-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center gap-1 touch-target"
                        >
                            <i data-lucide="eye" class="icon-responsive-xs"></i>
                            Detail
                        </a>

                        @if($order->status === 'completed')
                            <button
                                wire:click="downloadFiles({{ $order->id }})"
                                class="flex-1 btn-responsive-sm bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center justify-center gap-1 touch-target"
                            >
                                <i data-lucide="download" class="icon-responsive-xs"></i>
                                Download
                            </button>
                        @endif

                        <button
                            wire:click="reorder({{ $order->id }})"
                            class="flex-1 btn-responsive-sm bg-purple-600 hover:bg-purple-700 text-white rounded-lg flex items-center justify-center gap-1 touch-target"
                        >
                            <i data-lucide="copy" class="icon-responsive-xs"></i>
                            Pesan Lagi
                        </button>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State Mobile -->
            <div class="card-responsive text-center">
                <i data-lucide="history" class="icon-responsive-lg text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white mb-2">Tidak ada riwayat</h3>
                <p class="text-responsive-sm text-gray-500 dark:text-gray-400 mb-4">
                    @if($statusFilter !== 'all' || $search || $dateFilter !== 'all')
                        Tidak ada riwayat pesanan yang sesuai dengan filter yang dipilih.
                    @else
                        Belum ada riwayat pesanan yang selesai atau dibatalkan.
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-2 justify-center">
                    <a
                        href="{{ route('user.orders') }}"
                        class="btn-responsive-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg inline-flex items-center justify-center gap-2"
                    >
                        <i data-lucide="arrow-left" class="icon-responsive-xs"></i>
                        Lihat Pesanan Aktif
                    </a>
                    <a
                        href="{{ route('landing-page') }}"
                        class="btn-responsive-sm bg-green-600 hover:bg-green-700 text-white rounded-lg inline-flex items-center justify-center gap-2"
                    >
                        <i data-lucide="plus" class="icon-responsive-xs"></i>
                        Pesan Baru
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Orders List - Desktop View -->
    <div class="hide-on-mobile">
        <div class="card-responsive !p-0 overflow-hidden">
            @if($orders->count() > 0)
                <div class="table-responsive-container">
                    <table class="table-responsive">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Pesanan
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Paket
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tanggal Selesai
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-responsive-sm font-medium text-gray-900 dark:text-white">
                                                #{{ $order->order_number }}
                                            </div>
                                            <div class="text-responsive-sm text-gray-500 dark:text-gray-400">
                                                {{ $order->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                        <div class="text-responsive-sm text-gray-900 dark:text-white">
                                            {{ $order->package->name }}
                                        </div>
                                        @if($order->custom_package_name)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Custom: {{ $order->custom_package_name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                        <span class="badge-responsive inline-flex items-center rounded-full font-medium
                                            @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @elseif($order->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @endif">
                                            @if($order->status === 'completed')
                                                <i data-lucide="check-circle" class="icon-responsive-xs mr-1"></i>
                                            @else
                                                <i data-lucide="x-circle" class="icon-responsive-xs mr-1"></i>
                                            @endif
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-responsive-sm text-gray-500 dark:text-gray-400">
                                        @if($order->completed_at)
                                            {{ $order->completed_at->format('d M Y H:i') }}
                                        @else
                                            {{ $order->updated_at->format('d M Y H:i') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-responsive-sm text-gray-900 dark:text-white font-medium">
                                        {{ $order->display_total_price }}
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-responsive-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a
                                                href="{{ route('user.order-detail', $order) }}"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 touch-target"
                                            >
                                                <i data-lucide="eye" class="icon-responsive-xs"></i>
                                                Detail
                                            </a>

                                            @if($order->status === 'completed')
                                                <button
                                                    wire:click="downloadFiles({{ $order->id }})"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center gap-1 touch-target"
                                                >
                                                    <i data-lucide="download" class="icon-responsive-xs"></i>
                                                    Download
                                                </button>
                                            @endif

                                            <button
                                                wire:click="reorder({{ $order->id }})"
                                                class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 flex items-center gap-1 touch-target"
                                            >
                                                <i data-lucide="copy" class="icon-responsive-xs"></i>
                                                Pesan Lagi
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State Desktop -->
                <div class="text-center py-12">
                    <i data-lucide="history" class="icon-responsive-lg text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white mb-2">Tidak ada riwayat</h3>
                    <p class="text-responsive-sm text-gray-500 dark:text-gray-400 mb-4">
                        @if($statusFilter !== 'all' || $search || $dateFilter !== 'all')
                            Tidak ada riwayat pesanan yang sesuai dengan filter yang dipilih.
                        @else
                            Belum ada riwayat pesanan yang selesai atau dibatalkan.
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row gap-2 justify-center">
                        <a
                            href="{{ route('user.orders') }}"
                            class="btn-responsive-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg inline-flex items-center justify-center gap-2"
                        >
                            <i data-lucide="arrow-left" class="icon-responsive-xs"></i>
                            Lihat Pesanan Aktif
                        </a>
                        <a
                            href="{{ route('landing-page') }}"
                            class="btn-responsive-sm bg-green-600 hover:bg-green-700 text-white rounded-lg inline-flex items-center justify-center gap-2"
                        >
                            <i data-lucide="plus" class="icon-responsive-xs"></i>
                            Pesan Baru
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="mt-4 sm:mt-6">
                <div class="card-responsive !py-3">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Export Section -->
    <div class="card-responsive">
        <div class="flex-responsive-between">
            <div class="flex-1 min-w-0">
                <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white">Export Riwayat</h3>
                <p class="text-responsive-sm text-gray-500 dark:text-gray-400 mt-1 truncate">Download riwayat pesanan dalam format Excel</p>
            </div>
            <button
                wire:click="exportHistory"
                class="btn-responsive mt-4 md:mt-0 bg-green-600 hover:bg-green-700 text-white rounded-lg inline-flex items-center gap-2 touch-target"
            >
                <i data-lucide="download" class="icon-responsive-sm"></i>
                <span class="hidden xs:inline">Export Excel</span>
                <span class="xs:hidden">Export</span>
            </button>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed-bottom-nav show-on-mobile">
        <div class="flex justify-around items-center py-3">
            <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
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
            <a href="{{ route('user.history') }}" class="flex flex-col items-center text-blue-600 dark:text-blue-400">
                <i data-lucide="history" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Riwayat</span>
            </a>
            <a href="{{ route('user.feedback') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="message-square" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Feedback</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-16 sm:bottom-4 right-4 left-4 sm:left-auto z-50 max-w-full sm:max-w-sm"
        >
            <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="icon-responsive-md"></i>
                    <span class="text-responsive-sm font-medium">{{ session('message') }}</span>
                </div>
            </div>
        </div>
    @endif
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

        // Add touch feedback for buttons
        document.querySelectorAll('.touch-target').forEach(button => {
            button.addEventListener('touchstart', function() {
                this.classList.add('opacity-75', 'scale-95');
            });

            button.addEventListener('touchend', function() {
                this.classList.remove('opacity-75', 'scale-95');
            });

            button.addEventListener('touchcancel', function() {
                this.classList.remove('opacity-75', 'scale-95');
            });
        });

        // Handle export button
        const exportButton = document.querySelector('[wire\\:click="exportHistory"]');
        if (exportButton) {
            exportButton.addEventListener('click', function() {
                // Show loading state
                const originalHTML = this.innerHTML;
                this.innerHTML = `
                    <i data-lucide="loader" class="icon-responsive-sm animate-spin mr-2"></i>
                    <span>Memproses...</span>
                `;
                this.disabled = true;

                // Re-initialize icons
                if (window.Lucide) {
                    window.Lucide.createIcons();
                }

                // Restore after 3 seconds
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                    if (window.Lucide) {
                        window.Lucide.createIcons();
                    }
                }, 3000);
            });
        }
    });

    // Re-initialize icons on Livewire updates
    document.addEventListener('livewire:update', function() {
        if (window.Lucide) {
            window.Lucide.createIcons();
        }
    });
</script>
