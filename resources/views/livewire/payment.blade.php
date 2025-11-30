<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Pembayaran Pesanan
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Selesaikan pembayaran untuk melanjutkan proses pengerjaan
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Payment Main Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i data-lucide="package" class="w-5 h-5 mr-2"></i>
                        Detail Pesanan
                    </h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Nomor Pesanan</span>
                            <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Paket</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $order->package->name }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Project</span>
                            <span class="text-gray-900 dark:text-white">{{ $order->project_name }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Domain</span>
                            <span class="text-gray-900 dark:text-white">{{ $order->domain_name }}.yourdomain.com</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Durasi</span>
                            <span class="text-gray-900 dark:text-white">{{ $order->duration }} Bulan</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Harga Paket</span>
                            <span class="text-gray-900 dark:text-white">Rp {{ number_format($order->base_price, 0, ',', '.') }}</span>
                        </div>

                        @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-green-600 dark:text-green-400">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span class="text-gray-900 dark:text-white">Total Pembayaran</span>
                                <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                @if($errorMessage)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
                    <div class="flex items-start">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-500 mr-3 mt-0.5"></i>
                        <div>
                            <h3 class="text-red-800 dark:text-red-200 font-semibold mb-2">Terjadi Kesalahan</h3>
                            <p class="text-red-700 dark:text-red-300 text-sm">{{ $errorMessage }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payment Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center">
                        <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                        Instruksi Pembayaran
                    </h3>
                    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 text-green-500"></i>
                            Klik tombol "Bayar Sekarang" untuk diarahkan ke halaman pembayaran Midtrans
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 text-green-500"></i>
                            Pilih metode pembayaran yang tersedia (Transfer Bank, E-Wallet, Kartu Kredit)
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 text-green-500"></i>
                            Ikuti instruksi pembayaran hingga selesai
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 text-green-500"></i>
                            Status pembayaran akan diperbarui secara otomatis
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2 mt-0.5 text-green-500"></i>
                            Pembayaran akan kadaluarsa dalam 24 jam jika tidak diselesaikan
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Payment Action Sidebar -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pembayaran</h3>

                    @if($paymentUrl)
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Klik tombol di bawah untuk melanjutkan ke halaman pembayaran Midtrans.
                            </p>

                            <button wire:click="proceedToPayment" wire:loading.attr="disabled"
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 px-6 rounded-lg font-semibold text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-green-500/25 flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed">
                                <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i>
                                <span wire:loading.remove>Bayar Sekarang</span>
                                <span wire:loading class="flex items-center">
                                    <i data-lucide="loader" class="w-5 h-5 mr-2 animate-spin"></i>
                                    Mengarahkan...
                                </span>
                            </button>
                        </div>
                    @else
                        <div class="text-center py-4">
                            @if($isProcessing)
                                <div class="flex flex-col items-center justify-center text-blue-600 dark:text-blue-400 space-y-2">
                                    <i data-lucide="loader" class="w-8 h-8 animate-spin"></i>
                                    <span class="text-sm">Mempersiapkan pembayaran...</span>
                                    <span class="text-xs text-gray-500">Harap tunggu sebentar</span>
                                </div>
                            @else
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="alert-circle" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Gagal memuat halaman pembayaran.
                                </p>
                                <button wire:click="refreshPayment" wire:loading.attr="disabled"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                                    <span wire:loading.remove>Coba Lagi</span>
                                    <span wire:loading class="flex items-center">
                                        <i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>
                                        Memuat...
                                    </span>
                                </button>
                            @endif
                        </div>
                    @endif

                    <!-- Payment Status -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Status Pembayaran:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                Menunggu
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Merchant ID:</span>
                            <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ config('services.midtrans.merchant_id') }}</span>
                        </div>
                    </div>

                    <!-- Payment Methods Info -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Metode Pembayaran Tersedia:</h4>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded border">
                                <i data-lucide="landmark" class="w-4 h-4 mx-auto text-gray-600 dark:text-gray-400 mb-1"></i>
                                <span class="text-gray-600 dark:text-gray-400">Transfer Bank</span>
                            </div>
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded border">
                                <i data-lucide="credit-card" class="w-4 h-4 mx-auto text-gray-600 dark:text-gray-400 mb-1"></i>
                                <span class="text-gray-600 dark:text-gray-400">Kartu Kredit</span>
                            </div>
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded border">
                                <i data-lucide="smartphone" class="w-4 h-4 mx-auto text-gray-600 dark:text-gray-400 mb-1"></i>
                                <span class="text-gray-600 dark:text-gray-400">E-Wallet</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Info -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
                    <div class="flex items-start space-x-3">
                        <i data-lucide="help-circle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                        <div>
                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 text-sm mb-1">Butuh Bantuan?</h4>
                            <p class="text-yellow-700 dark:text-yellow-300 text-xs">
                                Hubungi support kami jika mengalami kendala dalam pembayaran.
                            </p>
                            <a href="https://wa.me/6282112345678" target="_blank"
                               class="inline-flex items-center text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 text-xs mt-2">
                                <i data-lucide="message-circle" class="w-3 h-3 mr-1"></i>
                                WhatsApp Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        if (window.Lucide) {
            window.Lucide.createIcons();
        }
    });

    // Auto refresh jika payment URL gagal generate
    setTimeout(() => {
        if (!@this.paymentUrl && !@this.isProcessing) {
            @this.refreshPayment();
        }
    }, 5000);
</script>
