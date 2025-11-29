<x-layouts.app :title="__('Pembayaran')">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-8">
        <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    Pembayaran
                </h1>
                <p class="text-lg text-gray-600">
                    Selesaikan pembayaran untuk melanjutkan proses pembuatan website
                </p>
            </div>

            <!-- Progress Steps -->
            <div class="max-w-2xl mx-auto mb-8">
                <div class="flex items-center justify-between">
                    <!-- Step 1 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                        </div>
                        <span class="ml-2 text-sm font-medium text-green-600">Pilih Paket</span>
                    </div>

                    <!-- Connector -->
                    <div class="flex-1 h-0.5 bg-green-500 mx-4"></div>

                    <!-- Step 2 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                        </div>
                        <span class="ml-2 text-sm font-medium text-green-600">Konfigurasi</span>
                    </div>

                    <!-- Connector -->
                    <div class="flex-1 h-0.5 bg-green-500 mx-4"></div>

                    <!-- Step 3 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-white text-sm font-medium">3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-blue-600">Pembayaran</span>
                    </div>

                    <!-- Connector -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>

                    <!-- Step 4 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500 text-sm font-medium">4</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Selesai</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <!-- Order Summary -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">No. Pesanan:</span>
                            <span class="font-medium text-gray-900">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Paket:</span>
                            <span class="font-medium text-gray-900">{{ $order->package->name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Project:</span>
                            <span class="font-medium text-gray-900">{{ $order->project_name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Domain:</span>
                            <span class="font-medium text-gray-900">{{ $order->domain_name }}.yourdomain.com</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="font-medium text-gray-900">{{ $order->duration }} Bulan</span>
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total Pembayaran:</span>
                                <span class="text-blue-600">{{ $order->display_total_price }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Action -->
                <div class="text-center">
                    @if($isProcessing)
                        <div class="py-8">
                            <i data-lucide="loader" class="w-12 h-12 text-blue-500 animate-spin mx-auto mb-4"></i>
                            <p class="text-gray-600">Mempersiapkan pembayaran...</p>
                        </div>
                    @elseif($paymentUrl)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-4">
                                Klik tombol di bawah untuk melanjutkan ke halaman pembayaran Midtrans
                            </p>
                            <button
                                wire:click="proceedToPayment"
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center"
                            >
                                <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i>
                                Bayar Sekarang
                            </button>
                        </div>
                        <p class="text-xs text-gray-500">
                            Anda akan diarahkan ke halaman pembayaran Midtrans yang aman
                        </p>
                    @else
                        <div class="py-8">
                            <i data-lucide="alert-circle" class="w-12 h-12 text-yellow-500 mx-auto mb-4"></i>
                            <p class="text-gray-600">Gagal memuat halaman pembayaran</p>
                            <button
                                wire:click="generatePaymentUrl"
                                class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors duration-200"
                            >
                                Coba Lagi
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Information Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start space-x-3">
                    <i data-lucide="info" class="w-6 h-6 text-blue-600 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-2">Informasi Pembayaran</h4>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Pembayaran diproses melalui Midtrans yang aman dan terpercaya</li>
                            <li>• Support berbagai metode pembayaran: Transfer Bank, E-Wallet, Credit Card</li>
                            <li>• Pesanan akan diproses setelah pembayaran berhasil</li>
                            <li>• Untuk bantuan, hubungi: support@example.com</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function() {
            // Initialize Lucide icons
            if (window.Lucide) {
                window.Lucide.createIcons();
            }

            Livewire.on('payment-url-generated', () => {
                // Optional: Auto-redirect after short delay
                setTimeout(() => {
                    Livewire.dispatch('proceed-to-payment');
                }, 2000);
            });

            Livewire.on('payment-error', (event) => {
                alert(event.message);
            });
        });
    </script>
</x-layouts.app>
