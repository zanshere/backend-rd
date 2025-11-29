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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                    Cari Feedback
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-4 w-4 text-zinc-400"></i>
                    </div>
                    <input
                        type="text"
                        id="search"
                        wire:model.live="search"
                        class="block w-full pl-10 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Cari feedback..."
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
                    <option value="feature">Feature Request</option>
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
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                        {{ $feedback->user->initials() }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $feedback->user->name }}
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $feedback->created_at->format('d M Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-auto">
                                        <!-- Type Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($feedback->type === 'suggestion') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($feedback->type === 'complaint') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @elseif($feedback->type === 'bug') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                            @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @endif">
                                            {{ ucfirst($feedback->type) }}
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
                                <div class="text-sm text-zinc-700 dark:text-zinc-300 mb-3">
                                    {{ $feedback->message }}
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    @if(!$feedback->is_read)
                                        <button
                                            wire:click="markAsRead({{ $feedback->id }})"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 text-xs"
                                        >
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                            Tandai Sudah Dibaca
                                        </button>
                                    @endif

                                    <button
                                        wire:click="respondToFeedback({{ $feedback->id }})"
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center gap-1 text-xs"
                                    >
                                        <i data-lucide="message-square" class="w-4 h-4"></i>
                                        Balas
                                    </button>

                                    <button
                                        wire:click="deleteFeedback({{ $feedback->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus feedback ini?"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1 text-xs"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Hapus
                                    </button>
                                </div>

                                <!-- Response (if exists) -->
                                @if($feedback->response)
                                    <div class="mt-4 p-3 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                                        <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Respon Admin:</div>
                                        <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $feedback->response }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                            {{ $feedback->responded_at->format('d M Y H:i') }}
                                        </div>
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
                <i data-lucide="message-square" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Tidak ada feedback</h3>
                <p class="text-zinc-500 dark:text-zinc-400">
                    @if($typeFilter || $search || $statusFilter)
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
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">
                    Balas Feedback dari {{ $selectedFeedback->user->name }}
                </h3>

                <div class="mb-4 p-3 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                    <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $selectedFeedback->message }}</div>
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
                        class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200"
                    >
                        Batal
                    </button>
                    <button
                        wire:click="sendResponse"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Kirim Balasan
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
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
</div>
