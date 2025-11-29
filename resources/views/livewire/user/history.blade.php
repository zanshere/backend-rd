<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Riwayat Pesanan</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Lihat riwayat semua pesanan yang telah selesai</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pesanan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <i data-lucide="package" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Selesai</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $completedOrders }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Dibatalkan</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $cancelledOrders }}</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $totalSpent }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="sr-only">Cari riwayat pesanan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-zinc-400"></i>
                    </div>
                    <input
                        type="text"
                        id="search"
                        wire:model.live="search"
                        class="block w-full pl-10 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Cari riwayat pesanan..."
                    />
                </div>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-64">
                <label for="statusFilter" class="sr-only">Filter status</label>
                <select
                    id="statusFilter"
                    wire:model.live="statusFilter"
                    class="block w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="all">Semua Status</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div class="w-full md:w-64">
                <label for="dateFilter" class="sr-only">Filter tanggal</label>
                <select
                    id="dateFilter"
                    wire:model.live="dateFilter"
                    class="block w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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

    <!-- Orders List -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Pesanan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Paket
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Tanggal Selesai
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($orders as $order)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            #{{ $order->order_number }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $order->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-white">
                                        {{ $order->package->name }}
                                    </div>
                                    @if($order->custom_package_name)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            Custom: {{ $order->custom_package_name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($order->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @endif">
                                        @if($order->status === 'completed')
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        @else
                                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                        @endif
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    @if($order->completed_at)
                                        {{ $order->completed_at->format('d M Y H:i') }}
                                    @else
                                        {{ $order->updated_at->format('d M Y H:i') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white font-medium">
                                    {{ $order->display_total_price }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('user.order-detail', $order) }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 text-xs"
                                        >
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                            Detail
                                        </a>

                                        @if($order->status === 'completed')
                                            <button
                                                wire:click="downloadFiles({{ $order->id }})"
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center gap-1 text-xs"
                                            >
                                                <i data-lucide="download" class="w-4 h-4"></i>
                                                Download
                                            </button>
                                        @endif

                                        <button
                                            wire:click="reorder({{ $order->id }})"
                                            class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 flex items-center gap-1 text-xs"
                                        >
                                            <i data-lucide="copy" class="w-4 h-4"></i>
                                            Pesan Lagi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i data-lucide="history" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Tidak ada riwayat</h3>
                <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                    @if($statusFilter !== 'all' || $search || $dateFilter !== 'all')
                        Tidak ada riwayat pesanan yang sesuai dengan filter yang dipilih.
                    @else
                        Belum ada riwayat pesanan yang selesai atau dibatalkan.
                    @endif
                </p>
                <a
                    href="{{ route('user.orders') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Lihat Pesanan Aktif
                </a>
            </div>
        @endif
    </div>

    <!-- Export Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Export Riwayat</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Download riwayat pesanan dalam format Excel</p>
            </div>
            <button
                wire:click="exportHistory"
                class="mt-4 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
            >
                <i data-lucide="download" class="w-4 h-4"></i>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
</div>
