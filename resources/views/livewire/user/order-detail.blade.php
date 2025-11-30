<div>
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Detail Pesanan #{{ $order->order_number }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ $order->project_name }} • {{ $order->domain_name }}.yourdomain.com
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $this->getStatusBadgeColor() }}-100 dark:bg-{{ $this->getStatusBadgeColor() }}-900 text-{{ $this->getStatusBadgeColor() }}-800 dark:text-{{ $this->getStatusBadgeColor() }}-200">
                        {{ $this->displayStatus }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $this->getPaymentStatusBadgeColor() }}-100 dark:bg-{{ $this->getPaymentStatusBadgeColor() }}-900 text-{{ $this->getPaymentStatusBadgeColor() }}-800 dark:text-{{ $this->getPaymentStatusBadgeColor() }}-200">
                        {{ $this->displayPaymentStatus }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                @foreach($this->progressSteps as $index => $step)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                {{ $step['status'] === 'completed' ? 'bg-green-500' :
                                   ($step['status'] === 'current' ? 'bg-blue-500' :
                                   ($step['status'] === 'cancelled' ? 'bg-red-500' : 'bg-gray-300 dark:bg-gray-600')) }}">
                                @if($step['status'] === 'completed')
                                    <i data-lucide="check" class="w-5 h-5 text-white"></i>
                                @elseif($step['status'] === 'cancelled')
                                    <i data-lucide="x" class="w-5 h-5 text-white"></i>
                                @else
                                    <span class="text-sm font-medium text-white">
                                        {{ $index + 1 }}
                                    </span>
                                @endif
                            </div>
                            <span class="mt-2 text-sm font-medium
                                {{ $step['status'] === 'completed' ? 'text-green-600 dark:text-green-400' :
                                   ($step['status'] === 'current' ? 'text-blue-600 dark:text-blue-400' :
                                   ($step['status'] === 'cancelled' ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400')) }}">
                                {{ $step['name'] }}
                            </span>
                            @if($step['date'])
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $step['date'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!$loop->last)
                        <div class="flex-1 h-1 mx-4
                            {{ $step['completed'] ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Package Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Informasi Paket
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Paket</label>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $order->package->name ?? 'Tidak Diketahui' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Harga</label>
                            <p class="text-gray-900 dark:text-white font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Proyek</label>
                            <p class="text-gray-900 dark:text-white">{{ $order->project_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Domain</label>
                            <p class="text-gray-900 dark:text-white">{{ $order->domain_name }}.yourdomain.com</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pesanan</label>
                            <p class="text-gray-900 dark:text-white">{{ $order->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Special Requirements -->
                @if($order->hasSpecialRequirements())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Kebutuhan Khusus
                    </h2>
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ $order->special_requirements['kebutuhan_khusus'] ?? '' }}
                    </p>
                </div>
                @endif

                <!-- Progress Updates -->
                @if($order->hasProgressUpdates())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Update Progress
                    </h2>
                    <div class="space-y-4">
                        @foreach($order->progressUpdates->sortByDesc('created_at') as $update)
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <p class="text-gray-700 dark:text-gray-300">{{ $update->notes }}</p>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $update->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                    Progress: {{ $update->progress_percentage }}%
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Aksi
                    </h3>
                    <div class="space-y-3">
                        @if($this->canBeCancelled)
                        <button wire:click="cancelOrder"
                                wire:confirm="Apakah Anda yakin ingin membatalkan pesanan ini?"
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                            Batalkan Pesanan
                        </button>
                        @endif

                        <a href="{{ route('user.orders') }}"
                           class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 text-center block">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Timeline
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">Dibuat</span>
                            <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->created_at->format('d M Y') }}
                            </span>
                        </div>

                        @if($order->paid_at)
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">Dibayar</span>
                            <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->paid_at->format('d M Y') }}
                            </span>
                        </div>
                        @endif

                        @if($order->isCompleted())
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">Selesai</span>
                            <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->updated_at->format('d M Y') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('message'))
    <div class="fixed top-4 right-4 z-50 max-w-sm">
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-lg">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2"></i>
                <span class="text-green-800 dark:text-green-200 text-sm font-medium">
                    {{ session('message') }}
                </span>
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
    });
</script>
