<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - Ryuzen Dev</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Berhasil!</h1>
                <p class="text-green-100">Terima kasih telah melakukan pembayaran</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Order Info -->
                <div class="bg-green-50 rounded-xl p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-green-700">No. Order</p>
                            <p class="font-semibold text-green-900">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-green-700">Total Bayar</p>
                            <p class="font-semibold text-green-900">{{ $order->display_total_price }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-green-700">Nama Proyek</p>
                            <p class="font-semibold text-green-900">{{ $order->project_name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i data-lucide="shopping-cart" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Pesanan Dibuat</p>
                            <p class="text-sm text-gray-500">{{ $order->formatted_created_at }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i data-lucide="credit-card" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Pembayaran Diterima</p>
                            <p class="text-sm text-gray-500">{{ $order->formatted_paid_at ?? 'Baru saja' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-lucide="settings" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Proses Pengerjaan</p>
                            <p class="text-sm text-gray-500">Tim kami akan segera memproses pesanan Anda</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('user.order-detail', ['order' => $order->id]) }}"
                       class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-200 flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                        Lihat Detail Pesanan
                    </a>

                    <a href="{{ route('user.dashboard') }}"
                       class="block w-full border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-3 px-4 rounded-lg text-center transition duration-200 flex items-center justify-center">
                        <i data-lucide="home" class="w-5 h-5 mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>

                <!-- Support Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 text-center">
                        Butuh bantuan?
                        <a href="mailto:support@ryuzen.dev" class="text-green-600 hover:text-green-800 font-medium">
                            Hubungi tim support kami
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">© 2025 Ryuzen Dev. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Auto redirect setelah 10 detik
        setTimeout(function() {
            window.location.href = "{{ route('user.order-detail', ['order' => $order->id]) }}";
        }, 10000);
    </script>
</body>
</html>
