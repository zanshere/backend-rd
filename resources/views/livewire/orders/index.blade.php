<!-- resources/views/orders/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pesanan Saya</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">Kelola semua pesanan website Anda di satu tempat</p>
                </div>
                <a href="{{ route('order.create') }}"
                   class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Pesan Baru
                </a>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="text"
                                   placeholder="Cari pesanan..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('orders.index') }}"
                           class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors">
                            Semua
                        </a>
                        @foreach(['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'progress' => 'Dikerjakan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $status => $label)
                        <a href="{{ route('orders.index', ['status' => $status]) }}"
                           class="px-4 py-2 rounded-lg {{ request('status') == $status ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        @if($orders->isEmpty())
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="package" class="w-12 h-12 text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Belum ada pesanan</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">Mulai buat pesanan pertama Anda untuk mengembangkan website</p>
            <a href="{{ route('order.create') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Buat Pesanan Baru
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($orders as $order)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $order->project_name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->order_number }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $order->payment_status_color }} bg-{{ $order->payment_status_color }}-100 dark:bg-{{ $order->payment_status_color }}-900 text-{{ $order->payment_status_color }}-800 dark:text-{{ $order->payment_status_color }}-200">
                                {{ $order->payment_status_display_name }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $order->status_badge_color }} bg-{{ $order->status_badge_color }}-100 dark:bg-{{ $order->status_badge_color }}-900 text-{{ $order->status_badge_color }}-800 dark:text-{{ $order->status_badge_color }}-200">
                                {{ $order->status_display_name }}
                            </span>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i data-lucide="package" class="w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="font-medium mr-2">Paket:</span>
                            {{ $order->package_name }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i data-lucide="globe" class="w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="font-medium mr-2">Domain:</span>
                            {{ $order->domain_name }}.com
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i data-lucide="calendar" class="w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="font-medium mr-2">Tanggal:</span>
                            {{ $order->formatted_created_at }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="font-medium mr-2">Total:</span>
                            {{ $order->display_total_price }}
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @if($order->latestOrderProgress)
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-2">
                            <span>Progress Pengerjaan</span>
                            <span>{{ $order->latestOrderProgress->percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ $order->latestOrderProgress->percentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $order->latestOrderProgress->title }}
                        </p>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('orders.show', $order->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                            Lihat Detail
                        </a>

                        @if($order->payment_status === 'pending' && $order->status === 'pending')
                        <a href="{{ route('payment.show', $order->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                            Bayar Sekarang
                        </a>
                        @endif

                        @if(in_array($order->status, ['draft', 'pending']))
                        <button wire:click="confirmCancel({{ $order->id }})"
                           class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Batalkan
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white dark:bg-gray-800">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Konfirmasi Pembatalan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-300 mb-4">
                Apakah Anda yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex justify-center gap-3">
                <button onclick="closeCancelModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Batal
                </button>
                <button id="confirmCancelBtn"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmCancel(orderId) {
        document.getElementById('cancelModal').classList.remove('hidden');
        document.getElementById('confirmCancelBtn').onclick = function() {
            Livewire.dispatch('cancelOrder', {orderId: orderId});
            closeCancelModal();
        };
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('cancelModal');
        if (event.target === modal) {
            closeCancelModal();
        }
    }

    // Initialize Lucide icons
    if (window.Lucide) {
        Lucide.createIcons();
    }
</script>
@endsection
