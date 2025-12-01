<div class="min-h-screen bg-gradient-to-br from-slate-50 to-red-50/30 dark:from-gray-900 dark:to-red-900/10 py-8">
    <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Error Icon -->
        <div class="w-24 h-24 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="x-circle" class="w-12 h-12 text-red-600 dark:text-red-400"></i>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Pembayaran Gagal</h1>

        @if($errorMessage)
            <p class="text-gray-600 dark:text-gray-300 mb-8">
                {{ $errorMessage }}
            </p>
        @elseif($order)
            <p class="text-gray-600 dark:text-gray-300 mb-8">
                Maaf, pembayaran untuk pesanan <strong>{{ $order->order_number }}</strong> tidak dapat diproses.
            </p>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 max-w-md mx-auto mb-8">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">No. Pesanan</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Paket</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->package->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total</span>
                        <span class="font-medium text-red-600 dark:text-red-400">{{ $order->display_total_price }}</span>
                    </div>
                </div>
            </div>

            <!-- Possible Reasons -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 max-w-2xl mx-auto mb-8">
                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-3">Kemungkinan Penyebab:</h3>
                <ul class="text-left text-yellow-700 dark:text-yellow-300 space-y-2">
                    <li class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 text-yellow-600 mt-0.5 flex-shrink-0"></i>
                        <span>Saldo kartu kredit atau e-wallet tidak mencukupi</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 text-yellow-600 mt-0.5 flex-shrink-0"></i>
                        <span>Transaksi ditolak oleh bank penerbit</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 text-yellow-600 mt-0.5 flex-shrink-0"></i>
                        <span>Waktu pembayaran telah kadaluarsa</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 text-yellow-600 mt-0.5 flex-shrink-0"></i>
                        <span>Terjadi kesalahan teknis pada sistem pembayaran</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button wire:click="retryPayment"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i>
                    Coba Pembayaran Lagi
                </button>
                <a href="{{ route('user.dashboard') }}"
                   class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        @endif

        <!-- Support Info -->
        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
            <p class="text-gray-600 dark:text-gray-300 mb-4">Masih mengalami masalah?</p>
            <a href="https://wa.me/6282112345678" target="_blank"
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i data-lucide="message-circle" class="w-5 h-5 mr-2"></i>
                Hubungi Support
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</div>
