<div>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-responsive safe-top">
        <div class="responsive-container max-w-6xl mx-auto">

            <!-- Breadcrumb -->
            <nav class="flex mb-4 sm:mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('user.orders') }}" class="inline-flex items-center text-responsive-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 touch-target">
                            <i data-lucide="arrow-left" class="icon-responsive-sm mr-2"></i>
                            <span class="hidden xs:inline">Kembali ke Pesanan</span>
                            <span class="xs:hidden">Kembali</span>
                        </a>
                    </li>
                </ol>
            </nav>

            <div class="sidebar-layout-responsive">

                <!-- Main Content -->
                <div class="sidebar-main-responsive space-y-4 sm:space-y-6 md:space-y-8">

                    <!-- Order Status Card -->
                    <div class="card-responsive">
                        <div class="flex-responsive-between mb-4 sm:mb-6">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-responsive-md font-bold text-gray-900 dark:text-white">Detail Pesanan</h2>
                                <p class="text-responsive-sm text-gray-600 dark:text-gray-300 mt-1 truncate">No: {{ $order->order_number }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-3 sm:mt-0">
                                <span class="badge-responsive inline-flex items-center rounded-full font-medium {{ $order->payment_status_badge_color }} bg-{{ $order->payment_status_badge_color }}-100 dark:bg-{{ $order->payment_status_badge_color }}-900">
                                    {{ $order->payment_status_display_name }}
                                </span>
                                <span class="badge-responsive inline-flex items-center rounded-full font-medium {{ $order->status_badge_color }} bg-{{ $order->status_badge_color }}-100 dark:bg-{{ $order->status_badge_color }}-900">
                                    {{ $order->status_display_name }}
                                </span>
                            </div>
                        </div>

                        <!-- Overall Progress -->
                        <div class="mb-4 sm:mb-6">
                            <div class="flex justify-between text-responsive-sm text-gray-600 dark:text-gray-300 mb-1 sm:mb-2">
                                <span>Progress Pengerjaan</span>
                                <span>{{ $order->overall_progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 sm:h-3">
                                <div class="bg-blue-500 h-2 sm:h-3 rounded-full transition-all duration-500"
                                     style="width: {{ $order->overall_progress }}%"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            @if($order->requiresPayment())
                            <a href="{{ route('payment.page', ['order' => $order->id]) }}"
                               class="btn-responsive bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 inline-flex items-center justify-center gap-2 touch-target">
                                <i data-lucide="credit-card" class="icon-responsive-sm"></i>
                                <span class="hidden xs:inline">Lanjutkan Pembayaran</span>
                                <span class="xs:hidden">Bayar</span>
                            </a>
                            @endif

                            @if($order->canBeCancelled())
                            <button wire:click="cancelOrder" wire:confirm="Apakah Anda yakin ingin membatalkan pesanan ini?"
                               class="btn-responsive bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 inline-flex items-center justify-center gap-2 touch-target">
                                <i data-lucide="trash-2" class="icon-responsive-sm"></i>
                                Batalkan Pesanan
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Timeline -->
                    <div class="card-responsive">
                        <h3 class="text-responsive-base font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Timeline Progress</h3>

                        @if($progressUpdates->isEmpty())
                        <div class="text-center py-6 sm:py-8">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <i data-lucide="clock" class="icon-responsive-md text-gray-400"></i>
                            </div>
                            <p class="text-responsive-sm text-gray-500 dark:text-gray-400">Belum ada update progress</p>
                            <p class="text-responsive-sm text-gray-400 dark:text-gray-500 mt-1 sm:mt-2">Tim akan mengupdate progress disini</p>
                        </div>
                        @else
                        <div class="space-y-4 sm:space-y-6">
                            @foreach($progressUpdates as $progress)
                            <div class="relative pl-6 sm:pl-8 pb-4 sm:pb-6 last:pb-0">
                                <div class="absolute left-1 sm:left-0 top-0 w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-blue-500"></div>
                                <div class="absolute left-2 sm:left-2 top-3 sm:top-4 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700 last:hidden"></div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 sm:p-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2">
                                        <h4 class="text-responsive-sm font-medium text-gray-900 dark:text-white">{{ $progress->title }}</h4>
                                        <span class="text-responsive-sm text-gray-500 dark:text-gray-400 mt-1 sm:mt-0">
                                            {{ $progress->created_at->format('d M Y H:i') }}
                                        </span>
                                    </div>

                                    @if($progress->description)
                                    <p class="text-responsive-sm text-gray-600 dark:text-gray-300 mb-2 sm:mb-3">{{ $progress->description }}</p>
                                    @endif

                                    <div class="flex items-center">
                                        <div class="flex-1 mr-3 sm:mr-4">
                                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 sm:h-2">
                                                <div class="bg-green-500 h-1.5 sm:h-2 rounded-full" style="width: {{ $progress->percentage }}%"></div>
                                            </div>
                                        </div>
                                        <span class="text-responsive-sm font-medium text-gray-700 dark:text-gray-300">{{ $progress->percentage }}%</span>
                                    </div>

                                    <div class="mt-2">
                                        <span class="badge-responsive inline-flex items-center rounded-full font-medium bg-{{ $progress->status_color }}-100 dark:bg-{{ $progress->status_color }}-900 text-{{ $progress->status_color }}-800 dark:text-{{ $progress->status_color }}-200">
                                            {{ $progress->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Auto-refresh notification -->
                        @if($isPolling && ($order->isInProgress() || $order->isConfirmed()))
                        <div class="mt-4 sm:mt-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-center">
                                <i data-lucide="refresh-cw" class="icon-responsive-sm text-blue-500 mr-2 animate-spin"></i>
                                <p class="text-responsive-sm text-blue-700 dark:text-blue-300">
                                    Progress akan diperbarui otomatis...
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Order Details -->
                    <div class="card-responsive">
                        <h3 class="text-responsive-base font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Detail Proyek</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 md:gap-6">
                            <div>
                                <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Nama Proyek</label>
                                <p class="text-responsive-sm text-gray-900 dark:text-white font-medium break-word">{{ $order->project_name }}</p>
                            </div>
                            <div>
                                <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Domain</label>
                                <p class="text-responsive-sm text-gray-900 dark:text-white font-medium break-word">{{ $order->domain_name }}.com</p>
                            </div>
                            <div>
                                <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Paket</label>
                                <p class="text-responsive-sm text-gray-900 dark:text-white font-medium break-word">{{ $order->package_name }}</p>
                            </div>
                            <div>
                                <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Tanggal Pesanan</label>
                                <p class="text-responsive-sm text-gray-900 dark:text-white font-medium">{{ $order->formatted_created_at }}</p>
                            </div>
                            @if($order->hasSpecialRequirements())
                            <div class="md:col-span-2">
                                <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Kebutuhan Khusus</label>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 sm:p-4">
                                    <p class="text-responsive-sm text-gray-700 dark:text-gray-300 whitespace-pre-line break-word">{{ $order->special_requirements_text }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="sidebar-aside-responsive space-y-4 sm:space-y-6 md:space-y-8">
                    <!-- Order Summary -->
                    <div class="card-responsive sticky top-4 sm:top-6 md:top-8">
                        <h4 class="text-responsive-base font-bold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center">
                            <i data-lucide="receipt" class="icon-responsive-sm mr-2"></i>
                            Ringkasan Biaya
                        </h4>

                        <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Harga Paket</span>
                                <span class="text-responsive-sm font-medium text-gray-900 dark:text-white">{{ $order->display_base_price }}</span>
                            </div>

                            @if($order->discount_amount > 0)
                            <div class="flex justify-between items-center text-green-600 dark:text-green-400">
                                <span class="text-responsive-sm">Diskon</span>
                                <span class="text-responsive-sm">- {{ $order->display_discount_amount }}</span>
                            </div>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-600 pt-3 sm:pt-4">
                                <div class="flex justify-between items-center text-responsive-base font-bold">
                                    <span class="text-gray-900 dark:text-white">Total</span>
                                    <span class="text-blue-600 dark:text-blue-400">{{ $order->display_total_price }}</span>
                                </div>
                            </div>

                            @if($order->isPaid())
                            <div class="pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Dibayar</span>
                                    <span class="text-responsive-sm font-medium text-green-600 dark:text-green-400">{{ $order->display_paid_amount }}</span>
                                </div>
                                <p class="text-responsive-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $order->formatted_paid_at }}
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- Package Info -->
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4 sm:pt-6">
                            <h5 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-2 sm:mb-3">Info Paket</h5>
                            <div class="space-y-1 sm:space-y-2 text-responsive-sm text-gray-600 dark:text-gray-300">
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="icon-responsive-xs mr-2 text-gray-400"></i>
                                    {{ $order->package->delivery_time }} hari pengerjaan
                                </div>
                                <div class="flex items-center">
                                    <i data-lucide="refresh-cw" class="icon-responsive-xs mr-2 text-gray-400"></i>
                                    {{ $order->package->revision_limit }} revisi gratis
                                </div>
                            </div>
                        </div>

                        <!-- Support Info -->
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4 sm:pt-6 mt-4 sm:mt-6">
                            <h5 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-2 sm:mb-3 flex items-center">
                                <i data-lucide="help-circle" class="icon-responsive-sm mr-2 text-blue-500"></i>
                                Butuh Bantuan?
                            </h5>
                            <p class="text-responsive-sm text-gray-600 dark:text-gray-300 mb-2 sm:mb-3">
                                Hubungi tim support kami untuk pertanyaan tentang pesanan Anda.
                            </p>
                            <div class="space-y-1 sm:space-y-2">
                                <a href="https://wa.me/6282112345678" target="_blank"
                                   class="inline-flex items-center text-responsive-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200 transition-colors touch-target">
                                    <i data-lucide="message-circle" class="icon-responsive-xs mr-2"></i>
                                    WhatsApp Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed-bottom-nav show-on-mobile">
        <div class="flex justify-around items-center py-3">
            <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="home" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Dashboard</span>
            </a>
            <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-blue-600 dark:text-blue-400">
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

    <!-- Auto-refresh script -->
    <script>
        document.addEventListener('livewire:initialized', function() {
            // Initialize Lucide icons
            if (window.Lucide) {
                window.Lucide.createIcons();
            }

            // Start polling jika order dalam progress
            @if($order->isInProgress() || $order->isConfirmed())
                @this.startPolling();
            @endif

            // Auto refresh progress
            let refreshInterval;
            let inactivityTimer;

            function startAutoRefresh() {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
                refreshInterval = setInterval(() => {
                    @this.checkForUpdates();
                }, 30000); // 30 seconds
            }

            function stopAutoRefresh() {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                    refreshInterval = null;
                }
            }

            function resetInactivityTimer() {
                if (inactivityTimer) {
                    clearTimeout(inactivityTimer);
                }
                inactivityTimer = setTimeout(() => {
                    if (@this.isPolling) {
                        startAutoRefresh();
                    }
                }, 60000); // 1 minute
            }

            // Start auto-refresh ketika polling aktif
            if (@this.isPolling) {
                startAutoRefresh();

                // Stop auto-refresh ketika user berinteraksi
                const interactionEvents = ['mousedown', 'touchstart', 'keydown', 'scroll'];
                interactionEvents.forEach(event => {
                    document.addEventListener(event, stopAutoRefresh);
                });

                // Restart auto-refresh setelah 1 menit tidak aktif
                interactionEvents.forEach(event => {
                    document.addEventListener(event, resetInactivityTimer);
                });
            }

            // Listen for new progress updates
            Livewire.on('new-progress-update', () => {
                // Show notification
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-16 sm:bottom-4 right-4 left-4 sm:left-auto z-50 max-w-full sm:max-w-sm fade-in-up';
                notification.innerHTML = `
                    <div class="bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg">
                        <div class="flex items-center gap-2">
                            <i data-lucide="refresh-cw" class="icon-responsive-md animate-spin"></i>
                            <span class="text-responsive-sm font-medium">Progress diperbarui</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(notification);

                // Re-initialize icons
                if (window.Lucide) {
                    window.Lucide.createIcons();
                }

                // Remove notification after 3 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 3000);

                // Play sound if available
                try {
                    const audio = new Audio('/notification.mp3');
                    audio.volume = 0.3;
                    audio.play().catch(() => {
                        // Silent fail if audio can't play
                    });
                } catch (e) {
                    // Silent fail
                }
            });

            // Listen for success messages
            Livewire.on('success', (event) => {
                const alert = document.createElement('div');
                alert.className = 'fixed bottom-16 sm:bottom-4 right-4 left-4 sm:left-auto z-50 max-w-full sm:max-w-sm fade-in-up';
                alert.innerHTML = `
                    <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="icon-responsive-md"></i>
                            <span class="text-responsive-sm font-medium">${event.message}</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(alert);

                // Re-initialize icons
                if (window.Lucide) {
                    window.Lucide.createIcons();
                }

                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                    // Only reload if it's a cancel order success
                    if (event.message.includes('dibatalkan')) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }, 3000);
            });

            // Listen for error messages
            Livewire.on('error', (event) => {
                const alert = document.createElement('div');
                alert.className = 'fixed bottom-16 sm:bottom-4 right-4 left-4 sm:left-auto z-50 max-w-full sm:max-w-sm fade-in-up';
                alert.innerHTML = `
                    <div class="bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg">
                        <div class="flex items-center gap-2">
                            <i data-lucide="alert-circle" class="icon-responsive-md"></i>
                            <span class="text-responsive-sm font-medium">${event.message}</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(alert);

                // Re-initialize icons
                if (window.Lucide) {
                    window.Lucide.createIcons();
                }

                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            });

            // Handle mobile bottom nav active state
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.fixed-bottom-nav a');

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (currentPath.includes(href) || currentPath.startsWith(href + '/')) {
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

            // Handle orientation change
            window.addEventListener('orientationchange', function() {
                // Re-initialize icons after orientation change
                setTimeout(() => {
                    if (window.Lucide) {
                        window.Lucide.createIcons();
                    }
                }, 300);
            });
        });

        // Re-initialize icons on Livewire updates
        document.addEventListener('livewire:update', function() {
            if (window.Lucide) {
                window.Lucide.createIcons();
            }
        });
    </script>
</div>
