<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Kritik & Saran</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola feedback dari user</p>
        </div>

        <!-- Stats -->
        <div class="flex items-center gap-4 mt-4 md:mt-0">
            <div class="text-center">
                <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalFeedbacks }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $unreadFeedbacks }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Belum Dibaca</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Cari Feedback
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="search"
                            wire:model.live="search"
                            class="block w-full pl-10 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            placeholder="Cari feedback, user, atau order..."
                        />
                    </div>
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="typeFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Tipe
                    </label>
                    <select
                        id="typeFilter"
                        wire:model.live="typeFilter"
                        class="block w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                        <option value="">Semua Tipe</option>
                        <option value="suggestion">Saran</option>
                        <option value="complaint">Keluhan</option>
                        <option value="bug">Bug</option>
                        <option value="feature">Permintaan Fitur</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Status
                    </label>
                    <select
                        id="statusFilter"
                        wire:model.live="statusFilter"
                        class="block w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                        <option value="">Semua Status</option>
                        <option value="unread">Belum Dibaca</option>
                        <option value="read">Sudah Dibaca</option>
                        <option value="responded">Sudah Direspon</option>
                    </select>
                </div>
            </div>

            <!-- Clear Filters Button -->
            @if($search || $typeFilter || $statusFilter)
                <div>
                    <button
                        wire:click="clearFilters"
                        class="px-4 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 border border-zinc-300 dark:border-zinc-600 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors"
                    >
                        Clear Filter
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Feedback List -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @if($feedbacks->count() > 0)
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($feedbacks as $feedback)
                    <div class="p-6 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors {{ $feedback->is_read ? '' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Header -->
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-200 dark:bg-blue-800 flex items-center justify-center text-blue-700 dark:text-blue-300 text-sm font-medium">
                                        {{ $feedback->user_initials }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                                {{ $feedback->user->name ?? 'User Tidak Ditemukan' }}
                                            </div>
                                            @if($feedback->order)
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded">
                                                    #{{ $feedback->order->order_number }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                            {{ $feedback->created_at->format('d M Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <!-- Type Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($feedback->type === 'suggestion') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($feedback->type === 'complaint') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @elseif($feedback->type === 'bug') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                            @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @else bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400
                                            @endif">
                                            {{ $feedback->type_label }}
                                        </span>

                                        <!-- Status Badge -->
                                        @if(!$feedback->is_read)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                Baru
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Message -->
                                <div class="text-sm text-zinc-700 dark:text-zinc-300 mb-4 whitespace-pre-wrap">
                                    {{ $feedback->message }}
                                </div>

                                <!-- Rating -->
                                @if($feedback->rating)
                                    <div class="flex items-center gap-1 mb-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $feedback->rating ? 'text-yellow-400 fill-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}"
                                                 fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-1">
                                            ({{ $feedback->rating }}/5)
                                        </span>
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex items-center gap-3">
                                    @if(!$feedback->is_read)
                                        <button
                                            wire:click="markAsRead({{ $feedback->id }})"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 text-xs px-3 py-1 border border-blue-200 dark:border-blue-800 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Tandai Sudah Dibaca
                                        </button>
                                    @else
                                        <button
                                            wire:click="markAsUnread({{ $feedback->id }})"
                                            class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-300 flex items-center gap-1 text-xs px-3 py-1 border border-zinc-200 dark:border-zinc-800 rounded hover:bg-zinc-50 dark:hover:bg-zinc-900/20 transition-colors"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Tandai Belum Dibaca
                                        </button>
                                    @endif

                                    <button
                                        wire:click="respondToFeedback({{ $feedback->id }})"
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center gap-1 text-xs px-3 py-1 border border-green-200 dark:border-green-800 rounded hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        Balas
                                    </button>

                                    <button
                                        wire:click="deleteFeedback({{ $feedback->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus feedback ini?"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1 text-xs px-3 py-1 border border-red-200 dark:border-red-800 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>

                                <!-- Response (if exists) -->
                                @if($feedback->response)
                                    <div class="mt-4 p-3 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                                Respon Admin:
                                                @if($feedback->responder)
                                                    <span class="text-zinc-700 dark:text-zinc-300">oleh {{ $feedback->responder->name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $feedback->responded_at?->format('d M Y H:i') ?? '' }}
                                            </div>
                                        </div>
                                        <div class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">{{ $feedback->response }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($feedbacks->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-zinc-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Tidak ada feedback</h3>
                <p class="text-zinc-500 dark:text-zinc-400">
                    @if($search || $typeFilter || $statusFilter)
                        Tidak ada feedback yang sesuai dengan filter yang dipilih.
                    @else
                        Belum ada feedback dari user.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Response Modal -->
    @if($selectedFeedback)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" x-data x-on:click="$wire.set('selectedFeedback', null)" x-on:click.self="$wire.set('selectedFeedback', null)">
            <div class="bg-white dark:bg-zinc-800 rounded-xl max-w-md w-full p-6" x-on:click.stop>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">
                    Balas Feedback dari {{ $selectedFeedback->user->name }}
                </h3>

                <div class="mb-4 p-3 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                    <div class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">{{ $selectedFeedback->message }}</div>
                </div>

                <textarea
                    wire:model="responseMessage"
                    rows="4"
                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    placeholder="Tulis balasan Anda..."
                ></textarea>

                <div class="flex justify-end gap-3 mt-4">
                    <button
                        wire:click="$set('selectedFeedback', null)"
                        class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 border border-zinc-300 dark:border-zinc-600 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors"
                    >
                        Batal
                    </button>
                    <button
                        wire:click="sendResponse"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-400 transition-colors flex items-center gap-2"
                    >
                        <svg wire:loading class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2"/>
                        </svg>
                        <span wire:loading.remove>Kirim Balasan</span>
                        <span wire:loading>Mengirim...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

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
