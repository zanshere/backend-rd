<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Manajemen Pesanan</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola semua pesanan dari user</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Cari Pesanan
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-zinc-400"></i>
                    </div>
                    <input
                        type="text"
                        id="search"
                        wire:model.live="search"
                        class="block w-full pl-10 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Cari pesanan, user, atau paket..."
                    />
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Status
                </label>
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

            <!-- User Filter -->
            <div>
                <label for="userFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    User
                </label>
                <select
                    id="userFilter"
                    wire:model.live="userFilter"
                    class="block w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Semua User</option>
                    @foreach($this->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
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
                                User
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
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                            {{ $order->user->initials() }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                                {{ $order->user->name }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $order->user->email }}
                                            </div>
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
                                        @elseif($order->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
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
                                        <!-- Quick Status Actions -->
                                        @if($order->status === 'pending')
                                            <button
                                                wire:click="updateOrderStatus({{ $order->id }}, 'accepted')"
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center gap-1 text-xs"
                                                title="Terima Pesanan"
                                            >
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                                Terima
                                            </button>
                                            <button
                                                wire:click="updateOrderStatus({{ $order->id }}, 'rejected')"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1 text-xs"
                                                title="Tolak Pesanan"
                                            >
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                                Tolak
                                            </button>
                                        @elseif($order->status === 'accepted')
                                            <button
                                                wire:click="updateOrderStatus({{ $order->id }}, 'progress')"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 text-xs"
                                                title="Mulai Pengerjaan"
                                            >
                                                <i data-lucide="play" class="w-4 h-4"></i>
                                                Mulai
                                            </button>
                                        @elseif(in_array($order->status, ['progress', 'revision']))
                                            <button
                                                wire:click="updateOrderStatus({{ $order->id }}, 'completed')"
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center gap-1 text-xs"
                                                title="Tandai Selesai"
                                            >
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                Selesai
                                            </button>
                                        @endif

                                        <!-- Detail Link -->
                                        <a
                                            href="{{ route('admin.order-progress', $order) }}"
                                            class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-300 flex items-center gap-1 text-xs"
                                            title="Kelola Progress"
                                        >
                                            <i data-lucide="settings" class="w-4 h-4"></i>
                                            Kelola
                                        </a>
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
                <p class="text-zinc-500 dark:text-zinc-400">
                    @if($statusFilter !== 'all' || $search || $userFilter)
                        Tidak ada pesanan yang sesuai dengan filter yang dipilih.
                    @else
                        Belum ada pesanan yang dibuat oleh user.
                    @endif
                </p>
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

    @if(session()->has('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
</div>
