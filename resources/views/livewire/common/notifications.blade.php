<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Notifikasi</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola notifikasi dan pengaturan preferensi Anda</p>
        </div>

        <!-- Quick Actions -->
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            @if($unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                >
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2"/>
                    </svg>
                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tandai Semua Dibaca
                </button>
            @endif

            @if($totalCount > 0)
                <button
                    wire:click="clearAll"
                    wire:confirm="Apakah Anda yakin ingin menghapus semua notifikasi?"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Semua
                </button>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Notifications -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $totalCount }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Unread Notifications -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Belum Dibaca</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $unreadCount }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today's Notifications -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Hari Ini</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $todayCount }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- This Week's Notifications -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Minggu Ini</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $weekCount }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Notifications List -->
        <div class="lg:col-span-2">
            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Tipe Notifikasi
                        </label>
                        <select
                            wire:model.live="typeFilter"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                            <option value="all">Semua Tipe</option>
                            <option value="order">Pesanan</option>
                            <option value="message">Pesan</option>
                            <option value="system">Sistem</option>
                            <option value="promotion">Promosi</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Status
                        </label>
                        <select
                            wire:model.live="statusFilter"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                            <option value="all">Semua Status</option>
                            <option value="unread">Belum Dibaca</option>
                            <option value="read">Sudah Dibaca</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Periode Waktu
                        </label>
                        <select
                            wire:model.live="dateFilter"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                            <option value="all">Semua Waktu</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                        </select>
                    </div>
                </div>

                <!-- Clear Filters -->
                @if($typeFilter !== 'all' || $statusFilter !== 'all' || $dateFilter !== 'all')
                    <div class="mt-4 flex justify-end">
                        <button
                            wire:click="resetFilters"
                            class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Filter
                        </button>
                    </div>
                @endif
            </div>

            <!-- Notifications List -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                @if($notifications->count() > 0)
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($notifications as $notification)
                            @php
                                $notificationData = $notification->data ?? [];
                                $type = $notificationData['type'] ?? 'system';
                                $icon = match($type) {
                                    'order' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                    'message' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                                    'promotion' => 'M15 17h5l-5 5v-5zM21 12c0-4.418-4.03-8-9-8s-9 3.582-9 8 4.03 8 9 8 9-3.582 9-8z',
                                    default => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
                                };
                                $colorClass = match($type) {
                                    'order' => 'text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/30',
                                    'message' => 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/30',
                                    'promotion' => 'text-purple-600 bg-purple-100 dark:text-purple-400 dark:bg-purple-900/30',
                                    default => 'text-orange-600 bg-orange-100 dark:text-orange-400 dark:bg-orange-900/30'
                                };
                            @endphp

                            <div class="p-6 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors {{ $notification->read_at ? '' : 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-500' }}">
                                <div class="flex items-start gap-4">
                                    <!-- Icon -->
                                    <div class="w-10 h-10 rounded-lg {{ $colorClass }} flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                        </svg>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-1">
                                                    {{ $notificationData['title'] ?? $notification->data['title'] ?? 'Notifikasi' }}
                                                </h3>
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                                                    {{ $notificationData['message'] ?? $notification->data['message'] ?? $notification->data['body'] ?? 'Tidak ada pesan' }}
                                                </p>

                                                @if(isset($notificationData['order_number']) || isset($notificationData['order_id']))
                                                    <div class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                                        Order #{{ $notificationData['order_number'] ?? $notificationData['order_id'] }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                                                @if(!$notification->read_at)
                                                    <button
                                                        wire:click="markAsRead('{{ $notification->id }}')"
                                                        wire:loading.attr="disabled"
                                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded transition-colors"
                                                        title="Tandai sudah dibaca"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                @endif

                                                <button
                                                    wire:click="deleteNotification('{{ $notification->id }}')"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus notifikasi ini?"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded transition-colors"
                                                    title="Hapus notifikasi"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Metadata -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                                                <span class="capitalize">{{ $type }}</span>
                                                <span>•</span>
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>

                                                @if(!$notification->read_at)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                        Baru
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($notifications->hasPages())
                        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-zinc-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                            @if($typeFilter !== 'all' || $statusFilter !== 'all' || $dateFilter !== 'all')
                                Tidak ada notifikasi yang sesuai dengan filter
                            @else
                                Tidak ada notifikasi
                            @endif
                        </h3>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                            @if($typeFilter !== 'all' || $statusFilter !== 'all' || $dateFilter !== 'all')
                                Coba ubah filter atau hapus filter untuk melihat semua notifikasi.
                            @else
                                Semua notifikasi Anda akan muncul di sini.
                            @endif
                        </p>
                        @if($typeFilter !== 'all' || $statusFilter !== 'all' || $dateFilter !== 'all')
                            <button
                                wire:click="resetFilters"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                            >
                                Tampilkan Semua Notifikasi
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Settings Sidebar -->
        <div class="space-y-6">
            <!-- Notification Settings -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Pengaturan Notifikasi</h3>

                <!-- Email Notifications -->
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Notifications
                    </h4>
                    <div class="space-y-3">
                        @foreach($emailSettings as $key => $setting)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $setting['label'] }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $setting['description'] }}</div>
                                </div>
                                <button
                                    wire:click="toggleEmailSetting('{{ $key }}')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $setting['enabled'] ? 'bg-blue-600' : 'bg-zinc-200 dark:bg-zinc-700' }}"
                                >
                                    <span class="sr-only">Toggle {{ $setting['label'] }}</span>
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $setting['enabled'] ? 'translate-x-5' : 'translate-x-0' }}"
                                    />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Push Notifications -->
                <div>
                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM21 12c0-4.418-4.03-8-9-8s-9 3.582-9 8 4.03 8 9 8 9-3.582 9-8z"/>
                        </svg>
                        Push Notifications
                    </h4>
                    <div class="space-y-3">
                        @foreach($pushSettings as $key => $setting)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $setting['label'] }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $setting['description'] }}</div>
                                </div>
                                <button
                                    wire:click="togglePushSetting('{{ $key }}')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $setting['enabled'] ? 'bg-blue-600' : 'bg-zinc-200 dark:bg-zinc-700' }}"
                                >
                                    <span class="sr-only">Toggle {{ $setting['label'] }}</span>
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $setting['enabled'] ? 'translate-x-5' : 'translate-x-0' }}"
                                    />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Save Settings Button -->
                <button
                    wire:click="saveSettings"
                    wire:loading.attr="disabled"
                    class="w-full mt-6 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2"/>
                    </svg>
                    <span wire:loading.remove>Simpan Pengaturan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>

            <!-- Notification Tips -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tips Notifikasi
                </h4>
                <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-2">
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Notifikasi pesan akan muncul saat admin membalas pesan Anda</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Notifikasi pesanan memberi tahu tentang status pesanan terbaru</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Notifikasi sistem berisi informasi penting dari platform</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span>•</span>
                        <span>Atur preferensi notifikasi sesuai kebutuhan Anda</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
        >
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
        >
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
</div>
