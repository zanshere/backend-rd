<div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50/30 dark:from-gray-900 dark:to-emerald-900/10">
    <!-- Header Section -->
    <div class="responsive-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <!-- Success Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 to-green-600 dark:from-emerald-600 dark:to-green-700 p-6 sm:p-8 mb-8 sm:mb-12 shadow-lg">
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-6 sm:mb-0">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                @if($order->status === \App\Models\Order::STATUS_CONFIRMED)
                                    <i data-lucide="check-circle" class="w-8 h-8 sm:w-10 sm:h-10 text-white"></i>
                                @else
                                    <i data-lucide="clock" class="w-8 h-8 sm:w-10 sm:h-10 text-white"></i>
                                @endif
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                                    @if($order->status === \App\Models\Order::STATUS_CONFIRMED)
                                        Pesanan Dikonfirmasi!
                                    @else
                                        Pembayaran Berhasil!
                                    @endif
                                </h1>
                                <p class="text-emerald-100 text-sm sm:text-base">
                                    @if($order->status === \App\Models\Order::STATUS_CONFIRMED)
                                        Pesanan Anda telah dikonfirmasi oleh admin. Tim kami akan segera memproses proyek Anda.
                                    @else
                                        Pembayaran telah berhasil diterima. Pesanan Anda sedang menunggu konfirmasi dari admin.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Order Info Quick View -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <p class="text-emerald-100 text-sm mb-1">No. Order</p>
                                <p class="font-bold text-white text-lg">{{ $order->order_number }}</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <p class="text-emerald-100 text-sm mb-1">Total Pembayaran</p>
                                <p class="font-bold text-white text-lg">{{ $order->display_total_price }}</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <p class="text-emerald-100 text-sm mb-1">Status Pesanan</p>
                                <p class="font-bold text-white text-lg">{{ $order->status_display_name }}</p>
                            </div>
                        </div>

                        <!-- Status Information -->
                        @if($order->status === \App\Models\Order::STATUS_PENDING)
                        <div class="mt-6 bg-yellow-500/20 backdrop-blur-sm rounded-xl p-4 border border-yellow-400/30">
                            <div class="flex items-center">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-300 mr-3"></i>
                                <div>
                                    <p class="font-medium text-yellow-100 mb-1">Menunggu Konfirmasi Admin</p>
                                    <p class="text-yellow-200 text-sm">
                                        Pesanan Anda akan diverifikasi dan dikonfirmasi oleh admin dalam waktu 1x24 jam.
                                        Anda akan mendapatkan notifikasi via email ketika pesanan telah dikonfirmasi.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @elseif($order->status === \App\Models\Order::STATUS_CONFIRMED)
                        <div class="mt-6 bg-purple-500/20 backdrop-blur-sm rounded-xl p-4 border border-purple-400/30">
                            <div class="flex items-center">
                                <i data-lucide="info" class="w-5 h-5 text-purple-300 mr-3"></i>
                                <div>
                                    <p class="font-medium text-purple-100 mb-1">Pesanan Telah Dikonfirmasi</p>
                                    <p class="text-purple-200 text-sm">
                                        Admin telah mengkonfirmasi pesanan Anda. Tim kami akan segera menghubungi Anda untuk
                                        membahas detail lebih lanjut dan memulai proses pengerjaan proyek.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Celebration Animation -->
                    <div class="hidden sm:block">
                        <div class="relative">
                            <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center animate-pulse">
                                @if($order->status === \App\Models\Order::STATUS_CONFIRMED)
                                    <i data-lucide="party-popper" class="w-12 h-12 text-white"></i>
                                @else
                                    <i data-lucide="clock" class="w-12 h-12 text-white"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="card-responsive mb-8">
            <h2 class="text-responsive-lg font-bold text-gray-900 dark:text-white mb-6">Status Pesanan</h2>

            <div class="space-y-6">
                <!-- Status Badges -->
                <div class="flex flex-wrap gap-3">
                    <div class="badge-responsive inline-flex items-center rounded-full font-medium bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 px-4 py-2">
                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                        {{ $order->payment_status_display_name }}
                    </div>
                    <div class="badge-responsive inline-flex items-center rounded-full font-medium
                        @if($order->status === \App\Models\Order::STATUS_CONFIRMED)
                            bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200
                        @elseif($order->status === \App\Models\Order::STATUS_PENDING)
                            bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                        @elseif($order->status === \App\Models\Order::STATUS_IN_PROGRESS)
                            bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                        @else
                            bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200
                        @endif px-4 py-2">
                        <i data-lucide="package" class="w-4 h-4 mr-2"></i>
                        {{ $order->status_display_name }}
                    </div>
                </div>

                <!-- Progress Bar -->
                <div>
                    <div class="flex justify-between text-responsive-sm text-gray-600 dark:text-gray-300 mb-2">
                        <span>Progress Pesanan</span>
                        <span>{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-500
                            @if($order->status === \App\Models\Order::STATUS_CONFIRMED)
                                bg-purple-500
                            @elseif($order->status === \App\Models\Order::STATUS_PENDING)
                                bg-yellow-500
                            @elseif($order->status === \App\Models\Order::STATUS_IN_PROGRESS)
                                bg-blue-500
                            @elseif($order->status === \App\Models\Order::STATUS_COMPLETED)
                                bg-green-500
                            @else
                                bg-gray-400
                            @endif"
                             style="width: {{ $progressPercentage }}%">
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        @if($order->status === \App\Models\Order::STATUS_PENDING)
                            ⏳ Menunggu konfirmasi dari admin - Proses 1x24 jam
                        @elseif($order->status === \App\Models\Order::STATUS_CONFIRMED)
                            ✅ Pesanan telah dikonfirmasi - Tim akan menghubungi Anda untuk memulai proyek
                        @elseif($order->status === \App\Models\Order::STATUS_IN_PROGRESS)
                            🚀 Proyek sedang dalam pengerjaan
                        @elseif($order->status === \App\Models\Order::STATUS_COMPLETED)
                            🎉 Proyek telah selesai
                        @else
                            Status: {{ $order->status_display_name }}
                        @endif
                    </p>
                </div>

                <!-- Progress Explanation -->
                @if($order->status === \App\Models\Order::STATUS_PENDING)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-2">Apa yang terjadi selanjutnya?</h3>
                    <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Admin akan memverifikasi pembayaran dan kelengkapan data pesanan Anda</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Setelah dikonfirmasi, Anda akan menerima notifikasi via email</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Tim kami akan menghubungi Anda untuk membahas detail proyek</span>
                        </li>
                    </ul>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Details Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Order Summary -->
            <div class="card-responsive lg:col-span-2">
                <h2 class="text-responsive-lg font-bold text-gray-900 dark:text-white mb-6">Detail Pesanan</h2>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Informasi Pesanan</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">No. Order</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Tanggal Order</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Metode Pembayaran</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Midtrans (Multi Payment)</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Informasi Project</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Nama Project</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->project_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Domain</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->domain_name }}.yourdomain.com</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Paket</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->package->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Rincian Harga</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-300">Harga Paket</span>
                                <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->base_price, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600 dark:text-green-400">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="font-bold text-gray-900 dark:text-white">Total Pembayaran</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Sidebar -->
            <div class="space-y-6">
                <!-- Download Invoice -->
                <div class="card-responsive">
                    <h3 class="text-responsive-base font-bold text-gray-900 dark:text-white mb-4">Aksi</h3>
                    <button wire:click="downloadInvoice"
                            class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                        <i data-lucide="download" class="w-5 h-5 mr-2"></i>
                        Unduh Invoice
                    </button>
                </div>

                <!-- Support Info -->
                <div class="card-responsive bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                    <h3 class="text-responsive-base font-bold text-blue-900 dark:text-blue-100 mb-3">Butuh Bantuan?</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mb-4">
                        Tim support kami siap membantu Anda 24/7
                    </p>
                    <div class="space-y-2">
                        <a href="https://wa.me/6282112345678" target="_blank"
                           class="flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                            <i data-lucide="message-circle" class="w-5 h-5 mr-2"></i>
                            WhatsApp Support
                        </a>
                        <a href="mailto:support@yourdomain.com"
                           class="flex items-center justify-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-medium transition-colors duration-200">
                            <i data-lucide="mail" class="w-5 h-5 mr-2"></i>
                            Email Support
                        </a>
                    </div>
                </div>

                <!-- Status Update -->
                @if($order->status === \App\Models\Order::STATUS_PENDING)
                <div class="card-responsive bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-start">
                        <i data-lucide="info" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5"></i>
                        <div>
                            <h3 class="font-medium text-yellow-800 dark:text-yellow-200 mb-1">Informasi Penting</h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                Pesanan Anda sedang dalam antrian konfirmasi. Proses ini biasanya memakan waktu 1-24 jam.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Timeline Progress Card -->
        <div class="card-responsive">
            <h2 class="text-responsive-lg font-bold text-gray-900 dark:text-white mb-6">Timeline Progress</h2>

            <div class="space-y-6">
                @foreach($timelineItems as $index => $item)
                    <div class="flex items-start">
                        <!-- Timeline Line -->
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2
                                @if($item['completed'])
                                    bg-emerald-500
                                @elseif($item['current'] && $item['color'] === 'yellow')
                                    bg-yellow-500
                                @elseif($item['current'] && $item['color'] === 'purple')
                                    bg-purple-500
                                @elseif($item['current'] && $item['color'] === 'blue')
                                    bg-blue-500
                                @else
                                    bg-gray-300 dark:bg-gray-600
                                @endif">
                                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 text-white"></i>
                            </div>
                            @if($index < count($timelineItems) - 1)
                                <div class="flex-grow w-0.5
                                    @if($timelineItems[$index + 1]['completed'])
                                        bg-emerald-500
                                    @else
                                        bg-gray-300 dark:bg-gray-600
                                    @endif"></div>
                            @endif
                        </div>

                        <!-- Timeline Content -->
                        <div class="flex-1 pb-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $item['title'] }}</h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $item['date'] }}</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">{{ $item['description'] }}</p>

                            <!-- Current Step Indicator -->
                            @if($item['current'])
                            <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                Sedang Berlangsung
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Auto Refresh jika menunggu konfirmasi -->
            @if($order->status === \App\Models\Order::STATUS_PENDING && $isPolling)
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-center">
                    <i data-lucide="refresh-cw" class="w-5 h-5 text-blue-500 mr-3 animate-spin"></i>
                    <div>
                        <p class="font-medium text-blue-800 dark:text-blue-300">Status akan diperbarui otomatis</p>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                            Sistem akan mengecek status konfirmasi setiap 30 detik
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer Navigation -->
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="{{ route('user.dashboard') }}"
               class="flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                Kembali ke Dashboard
            </a>
            <a href="{{ route('user.order-detail', ['order' => $order->id]) }}"
               class="flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                <i data-lucide="eye" class="w-5 h-5 mr-2"></i>
                Lihat Detail Pesanan
            </a>
        </div>
    </div>

    <!-- Copy Alert -->
    <div id="copyAlert" class="hidden fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i data-lucide="check" class="w-5 h-5 mr-2"></i>
            <span>Berhasil disalin ke clipboard!</span>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            if (window.lucide) {
                lucide.createIcons();
            }

            // Polling untuk cek status konfirmasi
            let pollingTimer;

            function startPollingInterval() {
                if (pollingTimer) {
                    clearInterval(pollingTimer);
                }

                pollingTimer = setInterval(() => {
                    if (!@this.isPolling) return;
                    @this.call('checkStatus');
                }, 30000); // 30 detik
            }

            function stopPolling() {
                if (pollingTimer) {
                    clearInterval(pollingTimer);
                    pollingTimer = null;
                }
            }

            // Start polling jika diperlukan
            @if($isPolling)
                startPollingInterval();
            @endif

            // Listen for order confirmed event
            Livewire.on('order-confirmed', () => {
                stopPolling();

                // Show success message
                const alert = document.createElement('div');
                alert.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50';
                alert.innerHTML = `
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                        <span>Pesanan telah dikonfirmasi admin!</span>
                    </div>
                `;
                document.body.appendChild(alert);

                if (window.lucide) {
                    lucide.createIcons();
                }

                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                    // Refresh page untuk update timeline
                    window.location.reload();
                }, 3000);
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (window.pollingTimer) {
                    clearInterval(window.pollingTimer);
                }
            });
        });
    </script>
</div>
