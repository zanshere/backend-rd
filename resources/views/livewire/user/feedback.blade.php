<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex-responsive-between">
        <div class="w-full">
            <h1 class="text-responsive-lg font-bold text-gray-900 dark:text-white">Kritik & Saran</h1>
            <p class="text-responsive-base text-gray-600 dark:text-gray-400 mt-1">Berikan masukan untuk meningkatkan layanan kami</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Feedback Form -->
        <div class="lg:col-span-2">
            <div class="card-responsive">
                <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white mb-3 sm:mb-4">Kirim Feedback</h3>

                <form wire:submit.prevent="submitFeedback" class="space-y-3 sm:space-y-4">
                    <!-- Type -->
                    <div>
                        <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipe Feedback <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            @foreach($feedbackTypes as $type => $label)
                                <label class="relative flex cursor-pointer touch-target">
                                    <input
                                        type="radio"
                                        wire:model="type"
                                        value="{{ $type }}"
                                        class="peer sr-only"
                                    >
                                    <div class="flex-1 text-center px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:ring-2 peer-checked:ring-blue-500/20 transition-all">
                                        <div class="text-responsive-sm font-medium text-gray-900 dark:text-white">{{ $label }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('type')
                            <p class="text-red-600 text-responsive-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Selection (Optional) -->
                    <div>
                        <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Terkait Pesanan (Opsional)
                        </label>
                        <select
                            wire:model="orderId"
                            class="form-select-responsive border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Pilih pesanan...</option>
                            @foreach($userOrders as $order)
                                <option value="{{ $order->id }}">#{{ $order->order_number }} - {{ $order->package->name ?? 'Package' }}</option>
                            @endforeach
                        </select>
                        @error('orderId')
                            <p class="text-red-600 text-responsive-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pesan Feedback <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="message"
                            rows="5"
                            class="form-input-responsive border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Tuliskan kritik, saran, atau masukan Anda secara detail..."
                        ></textarea>
                        @error('message')
                            <p class="text-red-600 text-responsive-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div class="text-responsive-sm text-gray-500 dark:text-gray-400 mt-1 flex justify-between">
                            <span>{{ strlen($message) }}/1000 karakter</span>
                            @if(strlen($message) > 800)
                            <span class="text-yellow-600">Mendekati batas</span>
                            @elseif(strlen($message) > 900)
                            <span class="text-red-600">Hampir penuh</span>
                            @endif
                        </div>
                    </div>

                    <!-- Rating -->
                    <div>
                        <label class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rating (Opsional)
                        </label>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    wire:click="setRating({{ $i }})"
                                    class="p-1 sm:p-1.5 transition-transform hover:scale-110 focus:outline-none touch-target"
                                >
                                    <svg class="icon-responsive-md {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
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
                                    class="ml-2 sm:ml-3 text-responsive-sm text-red-500 hover:text-red-700 touch-target"
                                >
                                    Hapus rating
                                </button>
                            @endif
                        </div>
                        @error('rating')
                            <p class="text-red-600 text-responsive-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submitFeedback"
                        class="w-full btn-responsive bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg transition-colors flex items-center justify-center gap-2 touch-target"
                    >
                        <span wire:loading.remove wire:target="submitFeedback">
                            <i data-lucide="send" class="icon-responsive-sm mr-2"></i>
                            Kirim Feedback
                        </span>
                        <span wire:loading wire:target="submitFeedback" class="flex items-center">
                            <i data-lucide="loader" class="icon-responsive-sm mr-2 animate-spin"></i>
                            Mengirim...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Feedback Guidelines -->
            <div class="card-responsive">
                <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white mb-3 sm:mb-4">Panduan Feedback</h3>

                <div class="space-y-2 sm:space-y-3">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mt-0.5 flex-shrink-0">
                            <svg class="icon-responsive-xs text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-responsive-sm font-medium text-gray-900 dark:text-white">Jelas dan Spesifik</div>
                            <div class="text-responsive-sm text-gray-500 dark:text-gray-400">Jelaskan dengan detail apa yang perlu ditingkatkan</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 sm:gap-3">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mt-0.5 flex-shrink-0">
                            <svg class="icon-responsive-xs text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-responsive-sm font-medium text-gray-900 dark:text-white">Konstruktif</div>
                            <div class="text-responsive-sm text-gray-500 dark:text-gray-400">Berikan saran yang membangun</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 sm:gap-3">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mt-0.5 flex-shrink-0">
                            <svg class="icon-responsive-xs text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-responsive-sm font-medium text-gray-900 dark:text-white">Respon Cepat</div>
                            <div class="text-responsive-sm text-gray-500 dark:text-gray-400">Kami akan merespon dalam 24 jam</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Feedback -->
            <div class="card-responsive">
                <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white mb-3 sm:mb-4">Feedback Terakhir</h3>

                <div class="space-y-3">
                    @forelse($recentFeedbacks as $feedback)
                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="badge-responsive inline-flex items-center rounded-full font-medium
                                    @if($feedback->type === 'suggestion') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($feedback->type === 'complaint') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @elseif($feedback->type === 'bug') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ $feedback->type_label }}
                                </span>
                                <div class="text-responsive-sm text-gray-500 dark:text-gray-400">
                                    {{ $feedback->created_at->format('d M') }}
                                </div>
                            </div>
                            <p class="text-responsive-sm text-gray-700 dark:text-gray-300 line-clamp-2 mb-2">
                                {{ \Illuminate\Support\Str::limit($feedback->message, 80) }}
                            </p>
                            @if($feedback->response)
                                <div class="text-responsive-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="icon-responsive-xs"></i>
                                    Sudah direspon
                                </div>
                            @else
                                <div class="text-responsive-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i data-lucide="clock" class="icon-responsive-xs"></i>
                                    Menunggu respon
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                            <i data-lucide="message-square" class="icon-responsive-lg mx-auto mb-2"></i>
                            <p class="text-responsive-sm">Belum ada feedback</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback History -->
    <div class="card-responsive !p-0 overflow-hidden">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white">Riwayat Feedback</h3>
        </div>

        @if($feedbacks->count() > 0)
            <!-- Mobile View -->
            <div class="show-on-mobile divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($feedbacks as $feedback)
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="badge-responsive inline-flex items-center rounded-full font-medium
                                    @if($feedback->type === 'suggestion') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($feedback->type === 'complaint') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @elseif($feedback->type === 'bug') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ $feedback->type_label }}
                                </span>
                                @if($feedback->rating)
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="icon-responsive-xs {{ $i <= $feedback->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                                 fill="currentColor"
                                                 viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            <div class="text-responsive-sm text-gray-500 dark:text-gray-400 text-right">
                                {{ $feedback->created_at->format('d M Y') }}
                            </div>
                        </div>

                        <div class="text-responsive-sm text-gray-700 dark:text-gray-300 mb-3 line-clamp-3">
                            {{ $feedback->message }}
                        </div>

                        @if($feedback->response)
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                                <div class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Respon Admin:</div>
                                <div class="text-responsive-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ $feedback->response }}</div>
                                <div class="text-responsive-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $feedback->responded_at?->format('d M Y') ?? '' }}
                                </div>
                            </div>
                        @else
                            <div class="text-responsive-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i data-lucide="clock" class="icon-responsive-xs"></i>
                                Menunggu respon admin
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Desktop View -->
            <div class="hide-on-mobile">
                <div class="table-responsive-container">
                    <table class="table-responsive">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Feedback
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tipe
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Rating
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-4 py-3 sm:px-6 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($feedbacks as $feedback)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 sm:px-6 sm:py-4">
                                        <div class="text-responsive-sm text-gray-900 dark:text-white font-medium mb-1">
                                            {{ \Illuminate\Support\Str::limit($feedback->message, 60) }}
                                        </div>
                                        @if($feedback->order)
                                            <div class="text-responsive-sm text-gray-500 dark:text-gray-400">
                                                #{{ $feedback->order->order_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                        <span class="badge-responsive inline-flex items-center rounded-full font-medium
                                            @if($feedback->type === 'suggestion') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($feedback->type === 'complaint') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @elseif($feedback->type === 'bug') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                            @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                            @endif">
                                            {{ $feedback->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                        @if($feedback->rating)
                                            <div class="flex items-center gap-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="icon-responsive-xs {{ $i <= $feedback->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                                         fill="currentColor"
                                                         viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        @else
                                            <span class="text-responsive-sm text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-responsive-sm text-gray-500 dark:text-gray-400">
                                        {{ $feedback->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap">
                                        @if($feedback->response)
                                            <span class="badge-responsive inline-flex items-center rounded-full font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <i data-lucide="check-circle" class="icon-responsive-xs mr-1"></i>
                                                Direspon
                                            </span>
                                        @else
                                            <span class="badge-responsive inline-flex items-center rounded-full font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <i data-lucide="clock" class="icon-responsive-xs mr-1"></i>
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($feedbacks->hasPages())
                <div class="px-4 py-3 sm:px-6 sm:py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-8 sm:py-12">
                <i data-lucide="message-square" class="icon-responsive-lg text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-responsive-base font-medium text-gray-900 dark:text-white mb-2">Belum ada feedback</h3>
                <p class="text-responsive-sm text-gray-500 dark:text-gray-400 mb-4">
                    Kirim feedback pertama Anda untuk membantu kami meningkatkan layanan.
                </p>
            </div>
        @endif
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed-bottom-nav show-on-mobile">
        <div class="flex justify-around items-center py-3">
            <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="home" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Dashboard</span>
            </a>
            <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="package" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Pesanan</span>
            </a>
            <a href="{{ route('landing-page') }}" class="flex flex-col items-center text-green-600 dark:text-green-400">
                <div class="bg-green-600 text-white p-3 rounded-full -mt-6 shadow-lg">
                    <i data-lucide="plus" class="icon-responsive-md"></i>
                </div>
                <span class="text-xs mt-2">Pesan</span>
            </a>
            <a href="{{ route('user.history') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                <i data-lucide="history" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Riwayat</span>
            </a>
            <a href="{{ route('user.feedback') }}" class="flex flex-col items-center text-blue-600 dark:text-blue-400">
                <i data-lucide="message-square" class="icon-responsive-md mb-1"></i>
                <span class="text-xs">Feedback</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-16 sm:bottom-4 right-4 left-4 sm:left-auto z-50 max-w-full sm:max-w-sm"
        >
            <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="icon-responsive-md"></i>
                    <span class="text-responsive-sm font-medium">{{ session('message') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-16 sm:bottom-4 right-4 left-4 sm:left-auto z-50 max-w-full sm:max-w-sm"
        >
            <div class="bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="icon-responsive-md"></i>
                    <span class="text-responsive-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        // Initialize Lucide icons
        if (window.Lucide) {
            window.Lucide.createIcons();
        }

        // Handle mobile bottom nav active state
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.fixed-bottom-nav a');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (currentPath === href || currentPath.startsWith(href + '/')) {
                link.classList.remove('text-gray-600', 'dark:text-gray-400');
                link.classList.add('text-blue-600', 'dark:text-blue-400');
            }
        });

        // Add touch feedback for buttons
        document.querySelectorAll('.touch-target').forEach(button => {
            button.addEventListener('touchstart', function() {
                this.classList.add('opacity-75', 'scale-95');
            });

            button.addEventListener('touchend', function() {
                this.classList.remove('opacity-75', 'scale-95');
            });

            button.addEventListener('touchcancel', function() {
                this.classList.remove('opacity-75', 'scale-95');
            });
        });

        // Handle textarea character count
        const textarea = document.querySelector('textarea[wire\\:model="message"]');
        if (textarea) {
            textarea.addEventListener('input', function() {
                const charCount = this.value.length;
                const counter = this.nextElementSibling?.querySelector('span');
                if (counter && charCount > 900) {
                    counter.classList.add('text-red-600');
                    counter.classList.remove('text-yellow-600');
                } else if (counter && charCount > 800) {
                    counter.classList.add('text-yellow-600');
                    counter.classList.remove('text-red-600');
                }
            });
        }

        // Handle rating buttons
        document.querySelectorAll('button[wire\\:click^="setRating"]').forEach(button => {
            button.addEventListener('touchstart', function() {
                this.classList.add('scale-125');
            });

            button.addEventListener('touchend', function() {
                this.classList.remove('scale-125');
            });
        });
    });

    // Re-initialize icons on Livewire updates
    document.addEventListener('livewire:update', function() {
        if (window.Lucide) {
            window.Lucide.createIcons();
        }
    });

    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        // Add slight delay for UI to adjust
        setTimeout(() => {
            if (window.Lucide) {
                window.Lucide.createIcons();
            }
        }, 100);
    });
</script>
