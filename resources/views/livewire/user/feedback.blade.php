<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Kritik & Saran</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Berikan masukan untuk meningkatkan layanan kami</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Feedback Form -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Kirim Feedback</h3>

                <form wire:submit.prevent="submitFeedback" class="space-y-4">
                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Tipe Feedback <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($feedbackTypes as $type => $label)
                                <label class="relative flex cursor-pointer">
                                    <input
                                        type="radio"
                                        wire:model="type"
                                        value="{{ $type }}"
                                        class="peer sr-only"
                                    >
                                    <div class="flex-1 text-center px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:ring-2 peer-checked:ring-blue-500/20 transition-all">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $label }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('type')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Selection (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Terkait Pesanan (Opsional)
                        </label>
                        <select
                            wire:model="orderId"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Pilih pesanan...</option>
                            @foreach($userOrders as $order)
                                <option value="{{ $order->id }}">#{{ $order->order_number }} - {{ $order->package->name ?? 'Package' }}</option>
                            @endforeach
                        </select>
                        @error('orderId')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Pesan Feedback <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="message"
                            rows="6"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Tuliskan kritik, saran, atau masukan Anda secara detail..."
                        ></textarea>
                        @error('message')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            {{ strlen($message) }}/1000 karakter
                        </div>
                    </div>

                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Rating (Opsional)
                        </label>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    wire:click="setRating({{ $i }})"
                                    class="p-1 transition-transform hover:scale-110 focus:outline-none"
                                >
                                    <svg class="w-6 h-6 {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}"
                                         fill="currentColor"
                                         viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            @if($rating > 0)
                                <button
                                    type="button"
                                    wire:click="setRating(0)"
                                    class="ml-2 text-xs text-red-500 hover:text-red-700"
                                >
                                    Hapus rating
                                </button>
                            @endif
                        </div>
                        @error('rating')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submitFeedback"
                        class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="submitFeedback">Kirim Feedback</span>
                        <span wire:loading wire:target="submitFeedback">Mengirim...</span>
                        <svg wire:loading.remove wire:target="submitFeedback" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg wire:loading wire:target="submitFeedback" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Feedback Guidelines -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Panduan Feedback</h3>

                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">Jelas dan Spesifik</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Jelaskan dengan detail apa yang perlu ditingkatkan</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">Konstruktif</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Berikan saran yang membangun</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">Respon Cepat</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Kami akan merespon dalam 24 jam</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Feedback -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Feedback Terakhir</h3>

                <div class="space-y-4">
                    @forelse($recentFeedbacks as $feedback)
                        <div class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($feedback->type === 'suggestion') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($feedback->type === 'complaint') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @elseif($feedback->type === 'bug') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400
                                    @endif">
                                    {{ $feedback->type_label }}
                                </span>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $feedback->created_at->format('d M') }}
                                </div>
                            </div>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 line-clamp-2">
                                {{ \Illuminate\Support\Str::limit($feedback->message, 80) }}
                            </p>
                            @if($feedback->response)
                                <div class="mt-2 text-xs text-green-600 dark:text-green-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Sudah direspon
                                </div>
                            @else
                                <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Menunggu respon
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-zinc-500 dark:text-zinc-400">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-sm">Belum ada feedback</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback History -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Riwayat Feedback</h3>
        </div>

        @if($feedbacks->count() > 0)
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($feedbacks as $feedback)
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($feedback->type === 'suggestion') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($feedback->type === 'complaint') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @elseif($feedback->type === 'bug') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400
                                    @endif">
                                    {{ $feedback->type_label }}
                                </span>
                                @if($feedback->order)
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                        #{{ $feedback->order->order_number }}
                                    </span>
                                @endif
                                @if($feedback->rating)
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $feedback->rating ? 'text-yellow-400 fill-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}"
                                                 fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $feedback->created_at->format('d M Y H:i') }}
                            </div>
                        </div>

                        <div class="text-sm text-zinc-700 dark:text-zinc-300 mb-3">
                            {{ $feedback->message }}
                        </div>

                        @if($feedback->response)
                            <div class="bg-zinc-50 dark:bg-zinc-700/50 p-3 rounded-lg">
                                <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Respon Admin:</div>
                                <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $feedback->response }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ $feedback->responded_at?->format('d M Y H:i') ?? '' }}
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Menunggu respon admin
                            </div>
                        @endif
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
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Belum ada feedback</h3>
                <p class="text-zinc-500 dark:text-zinc-400">
                    Kirim feedback pertama Anda untuk membantu kami meningkatkan layanan.
                </p>
            </div>
        @endif
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
