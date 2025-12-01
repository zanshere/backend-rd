<!-- resources/views/orders/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header dengan Breadcrumb -->
        <div class="mb-8">
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                            <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                            Pesanan
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 mx-1"></i>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Detail Pesanan</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Pesanan</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">No. {{ $order->order_number }}</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-3">
                    @if($order->payment_status === 'pending' && $order->status === 'pending')
                    <a href="{{ route('payment.show', $order->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                        Lanjutkan Pembayaran
                    </a>
                    @endif
                    <a href="{{ route('orders.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Order Status Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Status Pesanan</h2>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->payment_status_badge_color }} bg-{{ $order->payment_status_badge_color }}-100 dark:bg-{{ $order->payment_status_badge_color }}-900">
                                {{ $order->payment_status_display_name }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status_badge_color }} bg-{{ $order->status_badge_color }}-100 dark:bg-{{ $order->status_badge_color }}-900">
                                {{ $order->status_display_name }}
                            </span>
                        </div>
                    </div>

                    <!-- Progress Timeline -->
                    <div class="space-y-6">
                        <!-- Overall Progress -->
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-2">
                                <span>Progress Keseluruhan</span>
                                <span>{{ $order->overall_progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-blue-500 h-3 rounded-full transition-all duration-500"
                                     style="width: {{ $order->overall_progress }}%"></div>
                            </div>
                        </div>

                        <!-- Status Timeline -->
                        <div class="relative">
                            <!-- Timeline line -->
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                            <!-- Timeline items -->
                            @php
                                $statuses = [
                                    'draft' => ['label' => 'Draft', 'icon' => 'file-text', 'color' => 'gray'],
                                    'pending' => ['label' => 'Menunggu Pembayaran', 'icon' => 'clock', 'color' => 'yellow'],
                                    'confirmed' => ['label' => 'Dikonfirmasi', 'icon' => 'check-circle', 'color' => 'blue'],
                                    'progress' => ['label' => 'Dalam Pengerjaan', 'icon' => 'code', 'color' => 'indigo'],
                                    'completed' => ['label' => 'Selesai', 'icon' => 'check-circle-2', 'color' => 'green'],
                                ];
                                $currentStatus = array_search($order->status, array_keys($statuses));
                            @endphp

                            @foreach($statuses as $statusKey => $statusInfo)
                                @php
                                    $isActive = array_search($statusKey, array_keys($statuses)) <= $currentStatus;
                                    $isCurrent = $statusKey === $order->status;
                                @endphp
                                <div class="relative flex items-start mb-8 last:mb-0">
                                    <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $isActive ? "bg-{$statusInfo['color']}-500" : 'bg-gray-300 dark:bg-gray-600' }} mr-4">
                                        <i data-lucide="{{ $statusInfo['icon'] }}" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium {{ $isActive ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $statusInfo['label'] }}
                                        </h4>
                                        @if($isCurrent && $order->updated_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $isActive ? 'Sedang berlangsung' : '' }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Progress Updates -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Update Progress</h2>

                    @if($order->orderProgress->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="clock" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">Belum ada update progress</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Tim kami akan mengupdate progress pengerjaan disini</p>
                    </div>
                    @else
                    <div class="space-y-6">
                        @foreach($order->orderProgress->sortByDesc('created_at') as $progress)
                        <div class="border-l-2 border-blue-500 pl-4 py-2">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $progress->title }}</h4>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $progress->status_color }}-100 dark:bg-{{ $progress->status_color }}-900 text-{{ $progress->status_color }}-800 dark:text-{{ $progress->status_color }}-200">
                                        {{ $progress->status_label }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $progress->created_at->format('d M Y H:i') }}
                                    </span>
                                </div>
                            </div>
                            @if($progress->description)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $progress->description }}</p>
                            @endif
                            <div class="flex items-center">
                                <div class="flex-1 mr-4">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress->percentage }}%"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $progress->percentage }}%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Auto-refresh notification -->
                    <div class="mt-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-500 mr-2"></i>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Progress akan diperbarui secara otomatis. Halaman ini refresh setiap 30 detik.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Detail Proyek</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Proyek</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $order->project_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Domain</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $order->domain_name }}.com</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Paket</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $order->package_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durasi</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $order->duration }} Bulan</p>
                        </div>
                        @if($order->hasSpecialRequirements())
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kebutuhan Khusus</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $order->special_requirements['kebutuhan_khusus'] ?? '' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i data-lucide="receipt" class="w-5 h-5 mr-2"></i>
                        Ringkasan Pesanan
                    </h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Harga Paket</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $order->display_base_price }}</span>
                        </div>

                        @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-green-600 dark:text-green-400">
                            <span>Diskon</span>
                            <span>- {{ $order->display_discount_amount }}</span>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span class="text-gray-900 dark:text-white">Total</span>
                                <span class="text-blue-600 dark:text-blue-400">{{ $order->display_total_price }}</span>
                            </div>
                        </div>

                        @if($order->isPaid())
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Dibayar</span>
                                <span class="font-medium text-green-600 dark:text-green-400">{{ $order->display_paid_amount }}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $order->formatted_paid_at }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Action -->
                    @if($order->payment_status === 'pending' && $order->status === 'pending')
                    <div class="mb-6">
                        <a href="{{ route('payment.show', $order->id) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center transition-colors duration-200 shadow-lg shadow-green-500/25">
                            <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i>
                            Lanjutkan Pembayaran
                        </a>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                            Batas waktu pembayaran: 24 jam
                        </p>
                    </div>
                    @endif

                    <!-- Support Info -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-6 mt-6">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i data-lucide="help-circle" class="w-4 h-4 mr-2 text-blue-500"></i>
                            Butuh Bantuan?
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                            Hubungi tim support kami untuk pertanyaan tentang pesanan Anda.
                        </p>
                        <div class="space-y-2">
                            <a href="https://wa.me/6282112345678" target="_blank"
                               class="inline-flex items-center text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200 transition-colors">
                                <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i>
                                WhatsApp Support
                            </a>
                            <a href="mailto:support@ryuzen.dev"
                               class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors">
                                <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                support@ryuzen.dev
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Timeline Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2 text-blue-500"></i>
                        Timeline Proyek
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Dibuat</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->formatted_created_at }}</p>
                        </div>
                        @if($order->paid_at)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Dibayar</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->formatted_paid_at }}</p>
                        </div>
                        @endif
                        @if($order->package->delivery_time)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Estimasi Selesai</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $order->created_at->addDays($order->package->delivery_time)->format('d M Y') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh script -->
<script>
    // Auto refresh progress every 30 seconds
    let refreshInterval;

    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            location.reload();
        }, 30000); // 30 seconds
    }

    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    }

    // Start auto-refresh when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startAutoRefresh();

        // Stop auto-refresh when user is interacting with the page
        document.addEventListener('mousemove', stopAutoRefresh);
        document.addEventListener('keypress', stopAutoRefresh);

        // Restart auto-refresh after 1 minute of inactivity
        let inactivityTimer;
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                startAutoRefresh();
            }, 60000); // 1 minute
        }

        document.addEventListener('mousemove', resetInactivityTimer);
        document.addEventListener('keypress', resetInactivityTimer);
    });

    // Initialize Lucide icons
    if (window.Lucide) {
        Lucide.createIcons();
    }
</script>
@endsection
