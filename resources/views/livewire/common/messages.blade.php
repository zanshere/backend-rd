<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pesan</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola percakapan dengan admin</p>
        </div>

        <!-- New Message Button -->
        <button wire:click="$toggle('showNewMessageModal')"
            class="mt-4 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Pesan Baru
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Conversations List -->
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <!-- Conversations Header -->
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Percakapan</h3>
                </div>

                <!-- Conversations -->
                <div class="max-h-96 lg:max-h-[calc(100vh-300px)] overflow-y-auto">
                    @if ($conversations->count() > 0)
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($conversations as $conversation)
                                <button wire:click="selectConversation({{ $conversation->id }})"
                                    class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors {{ $selectedConversation?->id === $conversation->id ? 'bg-blue-50 dark:bg-blue-900/20 border-r-2 border-blue-500' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                            {{ $conversation->other_user->initials() }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                                    {{ $conversation->other_user->name }}
                                                </div>
                                                @if ($conversation->unread_count > 0)
                                                    <span
                                                        class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium bg-red-500 text-white rounded-full">
                                                        {{ $conversation->unread_count }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                                @if ($conversation->lastMessage)
                                                    {{ Str::limit($conversation->lastMessage->content, 30) }}
                                                @else
                                                    Tidak ada pesan
                                                @endif
                                            </div>
                                            <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                                @if ($conversation->lastMessage)
                                                    {{ $conversation->lastMessage->created_at->diffForHumans() }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-8 px-4">
                            <i data-lucide="message-square" class="w-12 h-12 text-zinc-400 mx-auto mb-3"></i>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Belum ada percakapan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="lg:col-span-3">
            @if ($selectedConversation)
                <div
                    class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden h-[calc(100vh-200px)] flex flex-col">
                    <!-- Messages Header -->
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                    {{ $selectedConversation->other_user->initials() }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $selectedConversation->other_user->name }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $selectedConversation->other_user->role === 'admin' ? 'Admin' : 'User' }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($selectedConversation->order)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        #{{ $selectedConversation->order->order_number }}
                                    </span>
                                @endif
                                <button wire:click="markAsRead"
                                    class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700"
                                    title="Tandai sudah dibaca">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messagesContainer">
                        @foreach ($messages as $message)
                            <div
                                class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%]">
                                    <div
                                        class="flex items-end gap-2 {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                        @if ($message->sender_id !== auth()->id())
                                            <div
                                                class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-xs font-medium mb-1">
                                                {{ $message->sender->initials() }}
                                            </div>
                                        @endif
                                        <div
                                            class="{{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' }} rounded-2xl px-4 py-2">
                                            <div class="text-sm">{{ $message->content }}</div>
                                            @if ($message->attachments->count() > 0)
                                                <div class="mt-2 space-y-1">
                                                    @foreach ($message->attachments as $attachment)
                                                        <div
                                                            class="flex items-center gap-2 p-2 bg-white/20 dark:bg-black/20 rounded-lg">
                                                            <i data-lucide="paperclip" class="w-3 h-3"></i>
                                                            <span
                                                                class="text-xs truncate">{{ $attachment->name }}</span>
                                                            <a href="{{ $attachment->url }}" target="_blank"
                                                                class="text-xs opacity-70 hover:opacity-100">
                                                                <i data-lucide="download" class="w-3 h-3"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div
                                        class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                        {{ $message->created_at->format('H:i') }}
                                        @if ($message->is_read && $message->sender_id === auth()->id())
                                            <i data-lucide="check-check" class="w-3 h-3 inline ml-1 text-blue-500"></i>
                                        @elseif($message->sender_id === auth()->id())
                                            <i data-lucide="check" class="w-3 h-3 inline ml-1 text-zinc-400"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($messages->count() === 0)
                            <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                                <i data-lucide="message-square" class="w-12 h-12 mx-auto mb-3"></i>
                                <p>Belum ada pesan dalam percakapan ini</p>
                            </div>
                        @endif
                    </div>

                    <!-- Message Input -->
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                        <form wire:submit="sendMessage" class="space-y-3">
                            <!-- Attachment Preview -->
                            @if ($attachments && count($attachments) > 0)
                                <div class="flex items-center gap-2 flex-wrap">
                                    @foreach ($attachments as $index => $attachment)
                                        <div
                                            class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700 rounded-lg text-sm">
                                            <i data-lucide="paperclip" class="w-4 h-4 text-zinc-500"></i>
                                            <span
                                                class="text-zinc-700 dark:text-zinc-300">{{ $attachment->getClientOriginalName() }}</span>
                                            <button type="button" wire:click="removeAttachment({{ $index }})"
                                                class="text-zinc-500 hover:text-red-500">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-end gap-3">
                                <!-- File Attachment -->
                                <label
                                    class="flex items-center justify-center w-10 h-10 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 cursor-pointer rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                    <i data-lucide="paperclip" class="w-5 h-5"></i>
                                    <input type="file" wire:model="attachments" multiple class="hidden">
                                </label>

                                <!-- Message Input -->
                                <div class="flex-1">
                                    <textarea wire:model="newMessage" rows="1" placeholder="Ketik pesan..."
                                        class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        x-data="{
                                            resize() {
                                                this.$el.style.height = 'auto';
                                                this.$el.style.height = this.$el.scrollHeight + 'px';
                                            }
                                        }" x-init="resize()" @input="resize()"></textarea>
                                </div>

                                <!-- Send Button -->
                                <button type="submit" wire:loading.attr="disabled"
                                    class="flex items-center justify-center w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- No Conversation Selected -->
                <div
                    class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 h-96 flex items-center justify-center">
                    <div class="text-center">
                        <i data-lucide="message-square" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Pilih percakapan</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                            Pilih percakapan dari daftar atau mulai percakapan baru
                        </p>
                        <button wire:click="$toggle('showNewMessageModal')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Pesan Baru
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- New Message Modal -->
    @if ($showNewMessageModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Pesan Baru</h3>

                <form wire:submit="startNewConversation" class="space-y-4">
                    <!-- Recipient -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Kepada
                        </label>
                        <select wire:model="recipientId"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                            <option value="">Pilih admin...</option>
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }} (Admin)</option>
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
                        <select wire:model="relatedOrderId"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih pesanan...</option>
                            @foreach ($userOrders as $order)
                                <option value="{{ $order->id }}">#{{ $order->order_number }} -
                                    {{ $order->package->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Pesan
                        </label>
                        <textarea wire:model="initialMessage" rows="4"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Tulis pesan pertama Anda..." required></textarea>
                        @error('initialMessage')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="$set('showNewMessageModal', false)"
                            class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            <span wire:loading.remove>Mulai Percakapan</span>
                            <span wire:loading>Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        // Scroll to bottom of messages
        Livewire.hook('morph.updated', (el) => {
            if (el.component && el.component.name === 'messages') {
                const messagesContainer = document.getElementById('messagesContainer');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }
        });

        // Auto-scroll when new messages are loaded
        Livewire.hook('commit', ({
            component,
            commit,
            respond
        }) => {
            respond(() => {
                if (component.name === 'messages') {
                    setTimeout(() => {
                        const messagesContainer = document.getElementById(
                            'messagesContainer');
                        if (messagesContainer) {
                            messagesContainer.scrollTop = messagesContainer
                            .scrollHeight;
                        }
                    }, 100);
                }
            });
        });
    });
</script>
