<div class="space-y-6" x-data="{
    onlineUsers: @entangle('onlineUsers').defer,
    showTyping: false,
    typingUser: null,
    init() {
        // Listen untuk events real-time
        this.$wire.on('users-updated', (users) => {
            this.onlineUsers = users;
        });

        this.$wire.on('user-joined', (user) => {
            if (!this.onlineUsers.find(u => u.id === user.id)) {
                this.onlineUsers.push(user);
            }
        });

        this.$wire.on('user-left', (user) => {
            this.onlineUsers = this.onlineUsers.filter(u => u.id !== user.id);
        });

        // Auto scroll to bottom when new messages arrive
        this.$watch('$wire.messagesList', () => {
            this.$nextTick(() => {
                this.scrollToBottom();
            });
        });

        // Scroll to bottom on initial load
        this.$nextTick(() => {
            this.scrollToBottom();
        });
    },
    scrollToBottom() {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    },
    isUserOnline(userId) {
        return this.onlineUsers.some(user => user.id === userId);
    }
}">
    <!-- Error/Success Messages - Fixed Position -->
    <div class="fixed top-4 right-4 z-50 space-y-2 max-w-sm" id="notificationContainer">
        <!-- Notifications will be inserted here by JavaScript -->
    </div>

    <!-- Online Users Indicator -->
    <div x-show="onlineUsers.length > 0" class="fixed top-20 right-4 z-40">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 p-3">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">Online</div>
            <div class="space-y-1">
                <template x-for="user in onlineUsers" :key="user.id">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-xs text-zinc-700 dark:text-zinc-300" x-text="user.name"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pesan</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola percakapan dengan admin</p>
        </div>

        <!-- New Message Button -->
        <button wire:click="$set('showNewMessageModal', true)"
            class="mt-4 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Pesan Baru
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Conversations List -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <!-- Conversations Header -->
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Percakapan</h3>
                </div>

                <!-- Conversations -->
                <div class="max-h-96 lg:max-h-[calc(100vh-300px)] overflow-y-auto"
                     wire:key="conversations-list">
                    @if (count($conversations) > 0)
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($conversations as $conversation)
                                @php
                                    // Handle array data properly
                                    $conversationId = $conversation['id'] ?? $conversation->id ?? null;
                                    $unreadCount = $conversation['unread_count'] ?? $conversation->unread_count ?? 0;
                                    $otherUser = $conversation['other_user'] ?? $conversation->other_user ?? null;
                                    $lastMessage = $conversation['last_message'] ?? $conversation->last_message ?? null;

                                    if (is_array($otherUser)) {
                                        $otherUserName = $otherUser['name'] ?? 'Unknown User';
                                        $otherUserId = $otherUser['id'] ?? null;
                                        $otherUserInitials = $otherUser['initials'] ?? '??';
                                        $otherUserRole = $otherUser['role'] ?? 'user';
                                    } else {
                                        $otherUserName = $otherUser->name ?? 'Unknown User';
                                        $otherUserId = $otherUser->id ?? null;
                                        $otherUserInitials = $otherUser->initials ?? '??';
                                        $otherUserRole = $otherUser->role ?? 'user';
                                    }

                                    if (is_array($lastMessage)) {
                                        $lastMessageContent = $lastMessage['content'] ?? '';
                                        $lastMessageCreatedAt = $lastMessage['created_at'] ?? null;
                                    } else {
                                        $lastMessageContent = $lastMessage->content ?? '';
                                        $lastMessageCreatedAt = $lastMessage->created_at ?? null;
                                    }
                                @endphp

                                @if($otherUser)
                                    <button wire:click="selectConversation({{ $conversationId }})"
                                            wire:key="conversation-{{ $conversationId }}"
                                            class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors {{ $selectedConversationId === $conversationId ? 'bg-blue-50 dark:bg-blue-900/20 border-r-2 border-blue-500' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                                    {{ $otherUserInitials }}
                                                </div>
                                                <!-- Online Indicator -->
                                                @if($otherUserId)
                                                    <div x-show="isUserOnline({{ $otherUserId }})"
                                                         class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full"></div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                                        {{ $otherUserName }}
                                                    </div>
                                                    @if ($unreadCount > 0)
                                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium bg-red-500 text-white rounded-full">
                                                            {{ $unreadCount }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                                    @if ($lastMessageContent)
                                                        {{ Str::limit($lastMessageContent, 30) }}
                                                    @else
                                                        Tidak ada pesan
                                                    @endif
                                                </div>
                                                <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                                    @if ($lastMessageCreatedAt)
                                                        {{ \Carbon\Carbon::parse($lastMessageCreatedAt)->diffForHumans() }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                @endif
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
            @if ($selectedConversationId && $selectedConversation)
                @php
                    // Handle selected conversation data properly
                    if (is_array($selectedConversation)) {
                        $selectedConversationId = $selectedConversation['id'] ?? null;
                        $otherUser = $selectedConversation['other_user'] ?? null;
                        $order = $selectedConversation['order'] ?? null;
                    } else {
                        $selectedConversationId = $selectedConversation->id ?? null;
                        $otherUser = $selectedConversation->other_user ?? null;
                        $order = $selectedConversation->order ?? null;
                    }

                    if (is_array($otherUser)) {
                        $otherUserName = $otherUser['name'] ?? 'Unknown User';
                        $otherUserId = $otherUser['id'] ?? null;
                        $otherUserInitials = $otherUser['initials'] ?? '??';
                        $otherUserRole = $otherUser['role'] ?? 'user';
                    } else {
                        $otherUserName = $otherUser->name ?? 'Unknown User';
                        $otherUserId = $otherUser->id ?? null;
                        $otherUserInitials = $otherUser->initials ?? '??';
                        $otherUserRole = $otherUser->role ?? 'user';
                    }

                    if (is_array($order)) {
                        $orderNumber = $order['order_number'] ?? null;
                    } else {
                        $orderNumber = $order->order_number ?? null;
                    }
                @endphp

                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden h-[calc(100vh-200px)] flex flex-col"
                     wire:key="conversation-{{ $selectedConversationId }}">
                    <!-- Messages Header -->
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                        {{ $otherUserInitials }}
                                    </div>
                                    <!-- Online Indicator -->
                                    @if($otherUserId)
                                        <div x-show="isUserOnline({{ $otherUserId }})"
                                             class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full"></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $otherUserName }}
                                        @if($otherUserId)
                                            <span x-show="isUserOnline({{ $otherUserId }})"
                                                  class="text-xs text-green-500 ml-1">• Online</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $otherUserRole === 'admin' ? 'Admin' : 'User' }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($orderNumber)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        #{{ $orderNumber }}
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
                            @php
                                // Handle message data properly
                                $messageId = $message['id'] ?? $message->id ?? null;
                                $messageContent = $message['content'] ?? $message->content ?? '';
                                $senderId = $message['sender_id'] ?? $message->sender_id ?? null;
                                $readAt = $message['read_at'] ?? $message->read_at ?? null;
                                $createdAt = $message['created_at'] ?? $message->created_at ?? null;
                                $sender = $message['sender'] ?? $message->sender ?? null;
                                $attachments = $message['attachments'] ?? $message->attachments ?? [];

                                if (is_array($sender)) {
                                    $senderInitials = $sender['initials'] ?? '??';
                                } else {
                                    $senderInitials = $sender->initials ?? '??';
                                }

                                $isOwnMessage = $senderId === auth()->id();
                            @endphp

                            <div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}"
                                 wire:key="message-{{ $messageId }}">
                                <div class="max-w-[70%]">
                                    <div class="flex items-end gap-2 {{ $isOwnMessage ? 'flex-row-reverse' : '' }}">
                                        @if (!$isOwnMessage && $sender)
                                            <div class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-xs font-medium mb-1">
                                                {{ $senderInitials }}
                                            </div>
                                        @endif
                                        <div class="{{ $isOwnMessage ? 'bg-blue-600 text-white' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' }} rounded-2xl px-4 py-2">
                                            <div class="text-sm">{{ $messageContent }}</div>
                                            @if (count($attachments) > 0)
                                                <div class="mt-2 space-y-1">
                                                    @foreach ($attachments as $attachment)
                                                        @php
                                                            if (is_array($attachment)) {
                                                                $attachmentId = $attachment['id'] ?? null;
                                                                $attachmentName = $attachment['original_name'] ?? 'Unknown File';
                                                            } else {
                                                                $attachmentId = $attachment->id ?? null;
                                                                $attachmentName = $attachment->original_name ?? 'Unknown File';
                                                            }
                                                        @endphp
                                                        <div class="flex items-center gap-2 p-2 bg-white/20 dark:bg-black/20 rounded-lg">
                                                            <i data-lucide="paperclip" class="w-3 h-3"></i>
                                                            <span class="text-xs truncate">{{ $attachmentName }}</span>
                                                            @if($attachmentId)
                                                                <button wire:click="downloadAttachment({{ $attachmentId }})"
                                                                    class="text-xs opacity-70 hover:opacity-100">
                                                                    <i data-lucide="download" class="w-3 h-3"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 {{ $isOwnMessage ? 'text-right' : 'text-left' }}">
                                        @if($createdAt)
                                            {{ \Carbon\Carbon::parse($createdAt)->format('H:i') }}
                                        @endif
                                        @if ($readAt && $isOwnMessage)
                                            <i data-lucide="check-check" class="w-3 h-3 inline ml-1 text-blue-500"></i>
                                        @elseif($isOwnMessage)
                                            <i data-lucide="check" class="w-3 h-3 inline ml-1 text-zinc-400"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Typing Indicator -->
                        <div x-show="showTyping" class="flex justify-start">
                            <div class="max-w-[70%]">
                                <div class="flex items-end gap-2">
                                    <div class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-xs font-medium mb-1">
                                        <span x-text="typingUser?.initials || '??'"></span>
                                    </div>
                                    <div class="bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white rounded-2xl px-4 py-2">
                                        <div class="flex space-x-1">
                                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"></div>
                                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (count($messages) === 0)
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
                                        <div class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700 rounded-lg text-sm">
                                            <i data-lucide="paperclip" class="w-4 h-4 text-zinc-500"></i>
                                            <span class="text-zinc-700 dark:text-zinc-300">{{ $attachment->getClientOriginalName() }}</span>
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
                                <label class="flex items-center justify-center w-10 h-10 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 cursor-pointer rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700">
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
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 h-96 flex items-center justify-center">
                    <div class="text-center">
                        <i data-lucide="message-square" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Pilih percakapan</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                            Pilih percakapan dari daftar atau mulai percakapan baru
                        </p>
                        <button wire:click="$set('showNewMessageModal', true)"
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
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Pesan Baru</h3>
                    <button wire:click="$set('showNewMessageModal', false)"
                        class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form wire:submit="startNewConversation" class="space-y-4">
                    <!-- Recipient -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Kepada <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="recipientId"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                            <option value="">Pilih admin...</option>
                            @foreach ($admins as $admin)
                                @php
                                    if (is_array($admin)) {
                                        $adminId = $admin['id'] ?? null;
                                        $adminName = $admin['name'] ?? 'Unknown Admin';
                                    } else {
                                        $adminId = $admin->id ?? null;
                                        $adminName = $admin->name ?? 'Unknown Admin';
                                    }
                                @endphp
                                <option value="{{ $adminId }}">{{ $adminName }} (Admin)</option>
                            @endforeach
                        </select>
                        @error('recipientId')
                            <p class="text-red-600 text-xs mt-1 flex items-center">
                                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                {{ $message }}
                            </p>
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
                                @php
                                    if (is_array($order)) {
                                        $orderId = $order['id'] ?? null;
                                        $orderNumber = $order['order_number'] ?? 'Unknown';
                                        $packageName = $order['package']['name'] ?? 'Unknown Package';
                                    } else {
                                        $orderId = $order->id ?? null;
                                        $orderNumber = $order->order_number ?? 'Unknown';
                                        $packageName = $order->package->name ?? 'Unknown Package';
                                    }
                                @endphp
                                <option value="{{ $orderId }}">#{{ $orderNumber }} - {{ $packageName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="initialMessage" rows="4"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Tulis pesan pertama Anda..." required></textarea>
                        @error('initialMessage')
                            <p class="text-red-600 text-xs mt-1 flex items-center">
                                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('showNewMessageModal', false)"
                            class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 border border-zinc-300 dark:border-zinc-600 rounded-lg">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span wire:loading.remove>Mulai Percakapan</span>
                            <span wire:loading>Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Initialize Lucide icons
        if (window.Lucide) {
            window.Lucide.createIcons();
        }

        // Handle success messages
        Livewire.on('show-success', (message) => {
            showNotification(message, 'success');
        });

        // Handle error messages
        Livewire.on('show-error', (message) => {
            showNotification(message, 'error');
        });

        // Handle info messages
        Livewire.on('show-info', (message) => {
            showNotification(message, 'info');
        });

        // Handle conversations updated
        Livewire.on('conversations-updated', () => {
            console.log('Conversations updated');
        });

        // Handle messages updated
        Livewire.on('messages-updated', () => {
            // Scroll to bottom after new message
            setTimeout(() => {
                const messagesContainer = document.getElementById('messagesContainer');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }, 100);
        });

        // Handle conversation changed
        Livewire.on('conversation-changed', (conversationId) => {
            console.log('Conversation changed to:', conversationId);
        });

        // Handle users updated
        Livewire.on('users-updated', (users) => {
            console.log('Online users updated:', users);
        });

        // Handle user joined
        Livewire.on('user-joined', (user) => {
            console.log('User joined:', user);
            showNotification(`${user.name} bergabung ke percakapan`, 'info');
        });

        // Handle user left
        Livewire.on('user-left', (user) => {
            console.log('User left:', user);
            showNotification(`${user.name} meninggalkan percakapan`, 'info');
        });

        // Notification function
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
            if (!container) return;

            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' :
                           type === 'error' ? 'bg-red-500' :
                           type === 'info' ? 'bg-blue-500' : 'bg-zinc-500';
            const icon = type === 'success' ? 'check-circle' :
                        type === 'error' ? 'alert-circle' :
                        type === 'info' ? 'info' : 'bell';

            notification.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out`;
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <i data-lucide="${icon}" class="w-5 h-5"></i>
                    <span class="text-sm font-medium">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto opacity-70 hover:opacity-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;

            container.appendChild(notification);

            // Re-initialize icons
            if (window.Lucide) {
                window.Lucide.createIcons();
            }

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Auto-scroll to bottom on page load
        setTimeout(() => {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }, 500);

        // Initialize Echo for real-time features
        function initializeEcho() {
            if (typeof Echo !== 'undefined') {
                console.log('Echo initialized for real-time messaging');
            } else {
                console.warn('Echo is not available. Make sure Laravel Echo is properly configured.');
            }
        }

        // Initialize Echo
        initializeEcho();
    });

    // Re-initialize icons after Livewire updates
    document.addEventListener('livewire:update', () => {
        if (window.Lucide) {
            window.Lucide.createIcons();
        }
    });

    // Handle page visibility change untuk optimisasi real-time
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Page menjadi visible, refresh data
            Livewire.dispatch('refreshConversations');
            Livewire.dispatch('refreshMessages');
        }
    });
</script>
