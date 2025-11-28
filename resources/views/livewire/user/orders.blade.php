<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pesanan Saya</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola dan pantau semua pesanan website Anda</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="sr-only">Cari pesanan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-zinc-400"></i>
                    </div>
                    <input
                        type="text"
                        id="search"
                        wire:model.live="search"
                        class="block w-full pl-10 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Cari pesanan..."
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
                    @foreach($this->statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
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
                                Progress
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
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @elseif($order->status === 'accepted') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($order->status === 'progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($order->status === 'revision') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                        @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                            <div
                                                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                style="width: {{ $order->progress_percentage }}%"
                                            ></div>
                                        </div>
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $order->progress_percentage }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white font-medium">
                                    {{ $order->display_total_price }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('user.order-detail', $order) }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1"
                                        >
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                            Detail
                                        </a>

                                        @if($order->status === 'pending')
                                            <button
                                                wire:click="cancelOrder({{ $order->id }})"
                                                wire:confirm="Apakah Anda yakin ingin membatalkan pesanan ini?"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1"
                                            >
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                                Batalkan
                                            </button>
                                        @endif
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
                <i data-lucide="package" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Tidak ada pesanan</h3>
                <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                    @if($statusFilter !== 'all' || $search)
                        Tidak ada pesanan yang sesuai dengan filter yang dipilih.
                    @else
                        Belum ada pesanan yang dibuat.
                    @endif
                </p>
                <a
                    href="{{ route('landing-page') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Pesanan Baru
                </a>
            </div>
        @endif
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
