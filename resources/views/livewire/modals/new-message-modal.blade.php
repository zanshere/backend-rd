<div>
    @if($showModal)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }" x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50" aria-hidden="true"></div>

            <!-- Modal Container -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <!-- Modal Content -->
                <div class="relative w-full max-w-md transform rounded-lg bg-white dark:bg-zinc-800 shadow-xl transition-all"
                    @click.away="show = false">

                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Pesan Baru</h3>
                            <button type="button" @click="$wire.closeModal()"
                                class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <form wire:submit.prevent="createConversation" class="space-y-4">
                            <!-- Recipient Selection -->
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Kepada (Admin) <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="recipientId"
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required
                                >
                                    <option value="">Pilih Admin...</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin['id'] }}">
                                            {{ $admin['name'] }}
                                            @if($admin['is_online'])
                                                <span class="text-green-600">• Online</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('recipientId')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Related Order (Optional) -->
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Terkait Pesanan (Opsional)
                                </label>
                                <select
                                    wire:model="relatedOrderId"
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Pilih Pesanan...</option>
                                    @foreach($userOrders as $order)
                                        <option value="{{ $order['id'] }}">
                                            #{{ $order['order_number'] }} - {{ $order['package_name'] }} ({{ $order['status'] }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('relatedOrderId')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message -->
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Pesan <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    wire:model="initialMessage"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                    placeholder="Tulis pesan Anda di sini..."
                                    required
                                ></textarea>
                                @error('initialMessage')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Loading Indicator -->
                            <div wire:loading wire:target="createConversation" class="text-center py-2">
                                <div class="inline-flex items-center gap-2 text-blue-600">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2" />
                                    </svg>
                                    <span>Membuat percakapan...</span>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    wire:click="cancel"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 border border-zinc-300 dark:border-zinc-600 rounded-lg disabled:opacity-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 min-w-[140px] justify-center"
                                >
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    <span wire:loading.remove>Kirim Pesan</span>
                                    <span wire:loading wire:target="createConversation">Mengirim...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
