<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-responsive safe-top">
    <div class="responsive-container max-w-4xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-responsive-lg font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                Pembayaran Pesanan
            </h1>
            <p class="text-responsive-base text-gray-600 dark:text-gray-300">
                Selesaikan pembayaran untuk melanjutkan proses pengerjaan
            </p>
        </div>

        <div class="sidebar-layout-responsive">

            <!-- Payment Main Content -->
            <div class="sidebar-main-responsive space-y-4 sm:space-y-6">

                <!-- Order Summary -->
                <div class="card-responsive">
                    <h2 class="text-responsive-md font-bold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center">
                        <i data-lucide="package" class="icon-responsive-sm mr-2"></i>
                        Detail Pesanan
                    </h2>

                    <div class="space-y-3 sm:space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Nomor Pesanan</span>
                            <span class="text-responsive-sm font-mono font-semibold text-gray-900 dark:text-white break-all">{{ $order->order_number }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Paket</span>
                            <span class="text-responsive-sm font-semibold text-gray-900 dark:text-white truncate">{{ $order->package->name }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Project</span>
                            <span class="text-responsive-sm text-gray-900 dark:text-white truncate">{{ $order->project_name }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Domain</span>
                            <span class="text-responsive-sm text-gray-900 dark:text-white truncate">{{ $order->domain_name }}.yourdomain.com</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Durasi</span>
                            <span class="text-responsive-sm text-gray-900 dark:text-white">{{ $order->duration }} Bulan</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Harga Paket</span>
                            <span class="text-responsive-sm text-gray-900 dark:text-white">Rp {{ number_format($order->base_price, 0, ',', '.') }}</span>
                        </div>

                        @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-green-600 dark:text-green-400">
                            <span class="text-responsive-sm">Diskon</span>
                            <span class="text-responsive-sm">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 dark:border-gray-600 pt-3 sm:pt-4">
                            <div class="flex justify-between items-center text-responsive-base font-bold">
                                <span class="text-gray-900 dark:text-white">Total Pembayaran</span>
                                <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                @if($errorMessage)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 sm:p-6">
                    <div class="flex items-start">
                        <i data-lucide="alert-triangle" class="icon-responsive-md text-red-500 mr-3 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-responsive-sm font-semibold text-red-800 dark:text-red-200 mb-1 sm:mb-2">Terjadi Kesalahan</h3>
                            <p class="text-responsive-sm text-red-700 dark:text-red-300 break-word">{{ $errorMessage }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payment Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 sm:p-6">
                    <h3 class="text-responsive-base font-semibold text-blue-900 dark:text-blue-100 mb-2 sm:mb-3 flex items-center">
                        <i data-lucide="info" class="icon-responsive-sm mr-2"></i>
                        Instruksi Pembayaran
                    </h3>
                    <ul class="text-responsive-sm text-blue-700 dark:text-blue-300 space-y-1 sm:space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="icon-responsive-xs text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Klik tombol "Bayar Sekarang" untuk diarahkan ke halaman pembayaran Midtrans</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="icon-responsive-xs text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Pilih metode pembayaran yang tersedia (Transfer Bank, E-Wallet, Kartu Kredit)</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="icon-responsive-xs text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Ikuti instruksi pembayaran hingga selesai</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="icon-responsive-xs text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Status pembayaran akan diperbarui secara otomatis</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="icon-responsive-xs text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                            <span>Pembayaran akan kadaluarsa dalam 24 jam jika tidak diselesaikan</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Payment Action Sidebar -->
            <div class="sidebar-aside-responsive space-y-4 sm:space-y-6">
                <div class="card-responsive sticky top-4 sm:top-6 md:top-8">
                    <h3 class="text-responsive-base font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">Pembayaran</h3>

                    @if($paymentUrl)
                        <div class="mb-4 sm:mb-6">
        <p class="text-responsive-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4">
            Klik tombol di bawah untuk melanjutkan ke halaman pembayaran Midtrans.
        </p>

        <button wire:click="proceedToPayment" wire:loading.attr="disabled"
            class="w-full btn-responsive-lg bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-green-500/25 flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed touch-target">
            <i data-lucide="credit-card" class="icon-responsive-sm mr-2"></i>
            <span wire:loading.remove>Bayar Sekarang</span>
            <span wire:loading class="flex items-center">
                <i data-lucide="loader" class="icon-responsive-sm mr-2 animate-spin"></i>
                Mengarahkan...
            </span>
        </button>
    </div>
                    @else
                        <div class="text-center py-4">
                            @if($isProcessing)
                                <div class="flex flex-col items-center justify-center text-blue-600 dark:text-blue-400 space-y-2">
                                    <i data-lucide="loader" class="icon-responsive-lg animate-spin"></i>
                                    <span class="text-responsive-sm">Mempersiapkan pembayaran...</span>
                                    <span class="text-responsive-sm text-gray-500">Harap tunggu sebentar</span>
                                </div>
                            @else
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                    <i data-lucide="alert-circle" class="icon-responsive-md text-gray-400"></i>
                                </div>
                                <p class="text-responsive-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4">
                                    Gagal memuat halaman pembayaran.
                                </p>
                                <button wire:click="refreshPayment" wire:loading.attr="disabled"
                                    class="w-full btn-responsive bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed touch-target">
                                    <i data-lucide="refresh-cw" class="icon-responsive-sm mr-2"></i>
                                    <span wire:loading.remove>Coba Lagi</span>
                                    <span wire:loading class="flex items-center">
                                        <i data-lucide="loader" class="icon-responsive-sm mr-2 animate-spin"></i>
                                        Memuat...
                                    </span>
                                </button>
                            @endif
                        </div>
                    @endif

                    <!-- Payment Status -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3 sm:pt-4 mt-3 sm:mt-4">
                        <div class="flex justify-between items-center mb-1 sm:mb-2">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Status Pembayaran:</span>
                            <span class="badge-responsive inline-flex items-center rounded-full font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <i data-lucide="clock" class="icon-responsive-xs mr-1"></i>
                                Menunggu
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-responsive-sm text-gray-600 dark:text-gray-400">Merchant ID:</span>
                            <span class="text-responsive-sm font-mono text-gray-500 dark:text-gray-400 truncate">{{ config('services.midtrans.merchant_id') }}</span>
                        </div>
                    </div>

                    <!-- Payment Methods Info -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3 sm:pt-4 mt-3 sm:mt-4">
                        <h4 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-2 sm:mb-3">Metode Pembayaran Tersedia:</h4>
                        <div class="grid grid-cols-3 gap-1 sm:gap-2 text-responsive-sm">
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded border">
                                <i data-lucide="landmark" class="icon-responsive-sm mx-auto text-gray-600 dark:text-gray-400 mb-1"></i>
                                <span class="text-gray-600 dark:text-gray-400 hidden xs:block">Transfer Bank</span>
                                <span class="text-gray-600 dark:text-gray-400 xs:hidden">Bank</span>
                            </div>
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded border">
                                <i data-lucide="credit-card" class="icon-responsive-sm mx-auto text-gray-600 dark:text-gray-400 mb-1"></i>
                                <span class="text-gray-600 dark:text-gray-400 hidden xs:block">Kartu Kredit</span>
                                <span class="text-gray-600 dark:text-gray-400 xs:hidden">Kartu</span>
                            </div>
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded border">
                                <i data-lucide="smartphone" class="icon-responsive-sm mx-auto text-gray-600 dark:text-gray-400 mb-1"></i>
                                <span class="text-gray-600 dark:text-gray-400 hidden xs:block">E-Wallet</span>
                                <span class="text-gray-600 dark:text-gray-400 xs:hidden">E-Wallet</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Info -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-3 sm:p-4">
                    <div class="flex items-start space-x-2 sm:space-x-3">
                        <i data-lucide="help-circle" class="icon-responsive-sm text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-responsive-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1">Butuh Bantuan?</h4>
                            <p class="text-responsive-sm text-yellow-700 dark:text-yellow-300">
                                Hubungi support kami jika mengalami kendala dalam pembayaran.
                            </p>
                            <a href="https://wa.me/6282112345678" target="_blank"
                               class="inline-flex items-center text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 text-responsive-sm mt-1 sm:mt-2 touch-target">
                                <i data-lucide="message-circle" class="icon-responsive-xs mr-1"></i>
                                WhatsApp Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Navigation -->
        <div class="fixed-bottom-nav show-on-mobile mt-8">
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
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        // Initialize icons
        if (window.Lucide) {
            window.Lucide.createIcons();
        }

        // Handle payment button click
        Livewire.on('payment-success', () => {
            // Show success message and redirect
            const message = document.createElement('div');
            message.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50';
            message.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 max-w-sm mx-4">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="check" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Pembayaran Berhasil!</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Anda akan diarahkan ke halaman konfirmasi...</p>
                        <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(message);

            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = "{{ route('payment.success', ['order' => $order->id]) }}";
            }, 2000);
        });

        // Handle payment error
        Livewire.on('payment-error', (data) => {
            // Show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                    <span class="text-sm">${data.message}</span>
                </div>
            `;
            document.body.appendChild(errorDiv);

            // Remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
        });

        // Handle payment URL generated
        Livewire.on('payment-url-generated', () => {
            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                    <span class="text-sm">URL pembayaran berhasil dibuat!</span>
                </div>
            `;
            document.body.appendChild(successDiv);

            // Remove after 3 seconds
            setTimeout(() => {
                if (successDiv.parentNode) {
                    successDiv.remove();
                }
            }, 3000);
        });
    });
</script>
