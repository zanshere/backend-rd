<div class="space-y-6" x-data="messageData()" x-init="init()">

    <!-- Fixed Elements -->
    <div class="fixed top-4 right-4 z-50 space-y-2 max-w-sm" id="notificationContainer"></div>
    <div x-show="onlineUsers.length > 0" class="fixed top-20 right-4 z-40">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 p-3">
            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                <span class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    Online (<span x-text="onlineUsers.length"></span>)
                </span>
            </div>
            <div class="space-y-1 max-h-60 overflow-y-auto">
                <template x-for="user in onlineUsers" :key="user.id">
                    <div class="flex items-center gap-2 py-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-zinc-700 dark:text-zinc-300" x-text="user.name"></span>
                        <span class="text-xs text-zinc-500" x-text="user.role === 'admin' ? '(Admin)' : ''"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div>
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pesan</h1>
                <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola percakapan dengan admin</p>
            </div>
            <button type="button"
                wire:click="openNewMessageModal"
                class="mt-4 md:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Pesan Baru
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
            <!-- Conversations List -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Percakapan</h3>
                    </div>
                    <div class="max-h-96 lg:max-h-[calc(100vh-300px)] overflow-y-auto" wire:key="conversations-list">
                        @if (count($conversationsList) > 0)
                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ($conversationsList as $conversation)
                                    @php
                                        $conversationId = $conversation['id'] ?? null;
                                        $unreadCount = $conversation['unread_count'] ?? 0;
                                        $otherUser = $conversation['other_user'] ?? null;
                                        $lastMessage = $conversation['last_message'] ?? null;

                                        if ($otherUser) {
                                            $otherUserName = $otherUser['name'] ?? 'Unknown User';
                                            $otherUserId = $otherUser['id'] ?? null;
                                            $otherUserInitials = $otherUser['initials'] ?? '??';
                                            $otherUserOnline = $otherUser['is_online'] ?? false;
                                            $otherUserLastSeen = $otherUser['last_seen'] ?? null;
                                            $otherUserAvatarColor = $otherUser['avatar_color'] ?? 'bg-zinc-200';
                                        }
                                    @endphp

                                    @if ($otherUser)
                                        <button wire:click="selectConversation({{ $conversationId }})"
                                            wire:key="conversation-{{ $conversationId }}"
                                            class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors {{ $selectedConversationId === $conversationId ? 'bg-blue-50 dark:bg-blue-900/20 border-r-2 border-blue-500' : '' }}">
                                            <div class="flex items-center gap-3">
                                                <div class="relative">
                                                    <div
                                                        class="w-10 h-10 rounded-full {{ $otherUserAvatarColor }} flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                                        {{ $otherUserInitials }}
                                                    </div>
                                                    <div x-show="isUserOnline({{ $otherUserId }})"
                                                        class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full">
                                                    </div>
                                                    <div x-show="!isUserOnline({{ $otherUserId }}) && {{ $otherUserOnline ? 'false' : 'true' }}"
                                                        class="absolute -bottom-1 -right-1 w-3 h-3 bg-gray-400 border-2 border-white dark:border-zinc-800 rounded-full">
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <div
                                                            class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                                                            {{ $otherUserName }}
                                                        </div>
                                                        @if ($unreadCount > 0)
                                                            <span
                                                                class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium bg-red-500 text-white rounded-full">
                                                                {{ $unreadCount }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                                        @if ($lastMessage && isset($lastMessage['content']))
                                                            {{ \Illuminate\Support\Str::limit($lastMessage['content'], 30) }}
                                                        @else
                                                            Tidak ada pesan
                                                        @endif
                                                    </div>
                                                    <div
                                                        class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 flex items-center gap-1">
                                                        @if ($otherUserOnline)
                                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                                                            <span>Online</span>
                                                        @elseif($otherUserLastSeen)
                                                            <span>{{ $otherUserLastSeen }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        @else
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
                @if ($selectedConversationId && $currentConversation)
                    @php
                        $otherUser = $currentConversation['other_user'] ?? null;
                        $order = $currentConversation['order'] ?? null;

                        if ($otherUser) {
                            $otherUserName = $otherUser['name'] ?? 'Unknown User';
                            $otherUserId = $otherUser['id'] ?? null;
                            $otherUserInitials = $otherUser['initials'] ?? '??';
                            $otherUserOnline = $otherUser['is_online'] ?? false;
                            $otherUserLastSeen = $otherUser['last_seen'] ?? null;
                            $otherUserAvatarColor = $otherUser['avatar_color'] ?? 'bg-zinc-200';
                        }
                    @endphp

                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden h-[calc(100vh-200px)] flex flex-col"
                        wire:key="conversation-{{ $selectedConversationId }}"
                        data-current-conversation="{{ $selectedConversationId }}">
                        <!-- Messages Header -->
                        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div
                                            class="w-8 h-8 rounded-full {{ $otherUserAvatarColor }} flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                                            {{ $otherUserInitials }}
                                        </div>
                                        <div x-show="isUserOnline({{ $otherUserId }})"
                                            class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full">
                                        </div>
                                        <div x-show="!isUserOnline({{ $otherUserId }}) && {{ $otherUserOnline ? 'false' : 'true' }}"
                                            class="absolute -bottom-1 -right-1 w-3 h-3 bg-gray-400 border-2 border-white dark:border-zinc-800 rounded-full">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $otherUserName }}
                                            <span x-show="isUserOnline({{ $otherUserId }})"
                                                class="text-xs text-green-500 ml-1">• Online</span>
                                            <span
                                                x-show="!isUserOnline({{ $otherUserId }}) && {{ $otherUserOnline ? 'false' : 'true' }}"
                                                class="text-xs text-gray-500 ml-1">• Offline</span>
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ ($otherUser['role'] ?? 'user') === 'admin' ? 'Admin' : 'User' }}
                                            @if (!$otherUserOnline && $otherUserLastSeen)
                                                • {{ $otherUserLastSeen }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if (isset($currentConversation['order']['order_number']))
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            #{{ $currentConversation['order']['order_number'] }}
                                        </span>
                                    @endif
                                    <button wire:click="markAsRead"
                                        class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700"
                                        title="Tandai sudah dibaca">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Typing Indicator -->
                            <div x-show="showTyping" class="mt-2 ml-11">
                                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <div class="flex space-x-1">
                                        <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"
                                            style="animation-delay: 0.1s"></div>
                                        <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"
                                            style="animation-delay: 0.2s"></div>
                                    </div>
                                    <span x-text="getTypingUsersText()"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Messages List -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messagesContainer">
                            @foreach ($messagesList as $message)
                                @php
                                    $messageId = $message['id'] ?? null;
                                    $messageContent = $message['content'] ?? '';
                                    $senderId = $message['sender_id'] ?? null;
                                    $readAt = $message['read_at'] ?? null;
                                    $createdAt = $message['created_at'] ?? null;
                                    $deliveryStatus = $message['delivery_status'] ?? 'sent';
                                    $formattedTime = $message['formatted_time'] ?? null;
                                    $isOwnMessage = $senderId === auth()->id();
                                @endphp

                                <div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}"
                                    wire:key="message-{{ $messageId }}">
                                    <div class="max-w-[70%]">
                                        <div
                                            class="flex items-end gap-2 {{ $isOwnMessage ? 'flex-row-reverse' : '' }}">
                                            @if (!$isOwnMessage)
                                                <div
                                                    class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-xs font-medium mb-1">
                                                    {{ $otherUserInitials }}
                                                </div>
                                            @endif
                                            <div
                                                class="{{ $isOwnMessage ? 'bg-blue-600 text-white' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' }} rounded-2xl px-4 py-2">
                                                <div class="text-sm">{{ $messageContent }}</div>
                                            </div>
                                        </div>
                                        <div
                                            class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 {{ $isOwnMessage ? 'text-right' : 'text-left' }} flex items-center gap-1 {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}">
                                            @if ($formattedTime)
                                                <span>{{ $formattedTime }}</span>
                                            @elseif($createdAt)
                                                <span>{{ \Carbon\Carbon::parse($createdAt)->format('H:i') }}</span>
                                            @endif

                                            @if ($isOwnMessage)
                                                <div class="flex items-center gap-0.5"
                                                    x-tooltip="getDeliveryStatusTooltip('{{ $deliveryStatus }}')">
                                                    <template x-if="'{{ $deliveryStatus }}' === 'sent'">
                                                        <i data-lucide="check" class="w-3 h-3"
                                                            :class="getDeliveryStatusColor('{{ $deliveryStatus }}')"></i>
                                                    </template>
                                                    <template x-if="'{{ $deliveryStatus }}' === 'delivered'">
                                                        <i data-lucide="check-check" class="w-3 h-3"
                                                            :class="getDeliveryStatusColor('{{ $deliveryStatus }}')"></i>
                                                    </template>
                                                    <template x-if="'{{ $deliveryStatus }}' === 'read'">
                                                        <i data-lucide="check-check" class="w-3 h-3"
                                                            :class="getDeliveryStatusColor('{{ $deliveryStatus }}')"></i>
                                                    </template>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Real-time Typing Indicator -->
                            <template x-for="(user, userId) in typingUsers" :key="userId">
                                <div class="flex justify-start" x-show="userId !== @json(auth()->id())">
                                    <div class="max-w-[70%]">
                                        <div class="flex items-end gap-2">
                                            <div
                                                class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-300 text-xs font-medium mb-1">
                                                <span x-text="user.initials"></span>
                                            </div>
                                            <div
                                                class="bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white rounded-2xl px-4 py-2">
                                                <div class="flex space-x-1">
                                                    <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"></div>
                                                    <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"
                                                        style="animation-delay: 0.1s"></div>
                                                    <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"
                                                        style="animation-delay: 0.2s"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            @if (count($messagesList) === 0)
                                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                                    <i data-lucide="message-square" class="w-12 h-12 mx-auto mb-3"></i>
                                    <p>Belum ada pesan dalam percakapan ini</p>
                                    <p class="text-sm mt-1">Mulai percakapan dengan mengetik pesan di bawah</p>
                                </div>
                            @endif
                        </div>

                        <!-- Message Input -->
                        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                            <form wire:submit="sendMessage" class="space-y-3">
                                @if ($attachments && count($attachments) > 0)
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @foreach ($attachments as $index => $attachment)
                                            <div
                                                class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700 rounded-lg text-sm">
                                                <i data-lucide="paperclip" class="w-4 h-4 text-zinc-500"></i>
                                                <span
                                                    class="text-zinc-700 dark:text-zinc-300">{{ $attachment->getClientOriginalName() }}</span>
                                                <button type="button"
                                                    wire:click="removeAttachment({{ $index }})"
                                                    class="text-zinc-500 hover:text-red-500">
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex items-end gap-3">
                                    <label
                                        class="flex items-center justify-center w-10 h-10 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 cursor-pointer rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                        <i data-lucide="paperclip" class="w-5 h-5"></i>
                                        <input type="file" wire:model="attachments" multiple class="hidden"
                                            x-ref="fileInput">
                                    </label>

                                    <div class="flex-1">
                                        <textarea wire:model="newMessage" rows="1" placeholder="Ketik pesan..."
                                            class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                            x-data="{
                                                resize() {
                                                    this.$el.style.height = 'auto';
                                                    this.$el.style.height = this.$el.scrollHeight + 'px';
                                                }
                                            }" x-init="resize()" @input="resize(); $dispatch('typing-started')"
                                            @blur="$dispatch('typing-stopped')"></textarea>
                                    </div>

                                    <button type="submit" wire:loading.attr="disabled"
                                        class="flex items-center justify-center w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50">
                                        <i data-lucide="send" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 h-96 flex items-center justify-center">
                        <div class="text-center">
                            <i data-lucide="message-square" class="w-16 h-16 text-zinc-400 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Pilih percakapan</h3>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-4">
                                Pilih percakapan dari daftar atau mulai percakapan baru
                            </p>
                            <button type="button"
                                wire:click="openNewMessageModal"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Pesan Baru
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function messageData() {
            return {
                onlineUsers: @json($onlineUsers),
                typingUsers: @json($typingUsers),
                showTyping: false,
                typingUser: null,
                typingTimeouts: {},

                init() {
                    this.setupTypingDetection();
                    this.initializeEventListeners();
                    this.scrollToBottomOnLoad();
                },

                setupTypingDetection() {
                    const textarea = this.$wire.$el.querySelector('textarea[name="newMessage"]');
                    if (textarea) {
                        let typingTimer;
                        const doneTypingInterval = 1000;

                        textarea.addEventListener('input', () => {
                            clearTimeout(typingTimer);
                            if (textarea.value.trim() !== '') {
                                if (!this.$wire.isTyping) {
                                    this.$wire.startTyping();
                                }
                                typingTimer = setTimeout(() => {
                                    this.$wire.stopTyping();
                                }, doneTypingInterval);
                            } else {
                                this.$wire.stopTyping();
                            }
                        });

                        textarea.addEventListener('blur', () => {
                            this.$wire.stopTyping();
                        });
                    }
                },

                initializeEventListeners() {
                    // Listen for Livewire events
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

                    this.$wire.on('user-typing', (data) => {
                        this.handleUserTyping(data);
                    });

                    this.$wire.on('user-stop-typing', (data) => {
                        this.handleUserStopTyping(data);
                    });

                    this.$wire.on('online-users-updated', (users) => {
                        this.onlineUsers = users;
                    });

                    this.$wire.on('user-online-joined', (user) => {
                        this.addOnlineUser(user);
                    });

                    this.$wire.on('user-online-left', (user) => {
                        this.removeOnlineUser(user.id);
                    });

                    this.$wire.on('user-online-status-changed', (data) => {
                        this.updateUserOnlineStatus(data);
                    });

                    this.$watch('$wire.messagesList', () => {
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    });
                },

                handleUserTyping(data) {
                    const userId = data.user_id;
                    this.typingUsers[userId] = data.user;
                    this.showTyping = true;
                    this.typingUser = data.user;

                    if (this.typingTimeouts[userId]) {
                        clearTimeout(this.typingTimeouts[userId]);
                    }

                    this.typingTimeouts[userId] = setTimeout(() => {
                        this.handleUserStopTyping(data);
                    }, 3000);
                },

                handleUserStopTyping(data) {
                    const userId = data.user_id;
                    delete this.typingUsers[userId];

                    if (this.typingTimeouts[userId]) {
                        clearTimeout(this.typingTimeouts[userId]);
                        delete this.typingTimeouts[userId];
                    }

                    this.showTyping = Object.keys(this.typingUsers).length > 0;
                    if (!this.showTyping) {
                        this.typingUser = null;
                    }
                },

                addOnlineUser(user) {
                    if (!this.onlineUsers.find(u => u.id === user.id)) {
                        this.onlineUsers.push(user);
                    }
                },

                removeOnlineUser(userId) {
                    const user = this.onlineUsers.find(u => u.id === userId);
                    if (user) {
                        this.onlineUsers = this.onlineUsers.filter(u => u.id !== userId);
                    }
                },

                updateUserOnlineStatus(data) {
                    const userId = data.user_id;
                    const isOnline = data.is_online;

                    if (isOnline) {
                        if (!this.onlineUsers.find(u => u.id === userId)) {
                            this.onlineUsers.push(data.user);
                        }
                    } else {
                        this.removeOnlineUser(userId);
                    }
                },

                scrollToBottomOnLoad() {
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
                },

                getTypingUsersText() {
                    const users = Object.values(this.typingUsers);
                    if (users.length === 0) return '';
                    if (users.length === 1) return users[0].name + ' sedang mengetik...';
                    if (users.length === 2) return users[0].name + ' dan ' + users[1].name + ' sedang mengetik...';
                    return users[0].name + ' dan ' + (users.length - 1) + ' lainnya sedang mengetik...';
                },

                getDeliveryStatusIcon(status) {
                    switch (status) {
                        case 'sent':
                            return 'check';
                        case 'delivered':
                            return 'check-check';
                        case 'read':
                            return 'check-check';
                        default:
                            return 'clock';
                    }
                },

                getDeliveryStatusColor(status) {
                    switch (status) {
                        case 'sent':
                            return 'text-gray-400';
                        case 'delivered':
                            return 'text-blue-500';
                        case 'read':
                            return 'text-green-500';
                        default:
                            return 'text-gray-400';
                    }
                },

                getDeliveryStatusTooltip(status) {
                    switch (status) {
                        case 'sent':
                            return 'Terkirim';
                        case 'delivered':
                            return 'Terkirim';
                        case 'read':
                            return 'Dibaca';
                        default:
                            return 'Mengirim...';
                    }
                }
            };
        }

        document.addEventListener('livewire:initialized', () => {
            if (window.Lucide) {
                window.Lucide.createIcons();
            }

            setupTypingDetection();
            setupNotificationHandlers();
            setupEventListeners();
            initializeRealTime();
        });

        function setupTypingDetection() {
            let typingTimer;
            const doneTypingInterval = 1000;

            // Gunakan event delegation untuk menghindari error
            document.addEventListener('input', function(e) {
                const target = e.target;
                if (target.tagName === 'TEXTAREA' && target.name === 'newMessage') {
                    clearTimeout(typingTimer);

                    if (target.value.trim() !== '') {
                        Livewire.dispatch('user-is-typing');
                        typingTimer = setTimeout(() => {
                            Livewire.dispatch('user-stopped-typing');
                        }, doneTypingInterval);
                    } else {
                        Livewire.dispatch('user-stopped-typing');
                    }
                }
            });

            document.addEventListener('blur', function(e) {
                const target = e.target;
                if (target.tagName === 'TEXTAREA' && target.name === 'newMessage') {
                    Livewire.dispatch('user-stopped-typing');
                }
            });
        }

        function setupNotificationHandlers() {
            Livewire.on('show-success', (message) => {
                showNotification(message, 'success');
            });

            Livewire.on('show-error', (message) => {
                showNotification(message, 'error');
            });

            Livewire.on('show-info', (message) => {
                showNotification(message, 'info');
            });
        }

        function setupEventListeners() {
            Livewire.on('conversations-updated', () => {
                console.log('Conversations updated');
            });

            Livewire.on('messages-updated', () => {
                setTimeout(() => {
                    scrollToBottom();
                }, 100);
            });

            Livewire.on('scroll-to-bottom', () => {
                scrollToBottom();
            });
        }

        function initializeRealTime() {
            if (typeof Echo !== 'undefined') {
                console.log('Echo initialized for real-time messaging');
            } else {
                console.warn('Echo is not available.');
            }
        }

        function scrollToBottom() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }

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

            notification.className = bgColor +
                ' text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-0';
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

            if (window.Lucide) {
                window.Lucide.createIcons();
            }

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

        document.addEventListener('livewire:update', () => {
            if (window.Lucide) {
                window.Lucide.createIcons();
            }
        });

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                Livewire.dispatch('refreshConversations');
                Livewire.dispatch('refreshMessages');
            }
        });
    </script>

    <style>
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }

        .online-indicator {
            transition: all 0.3s ease;
        }

        .online-indicator.online {
            background-color: #10B981;
        }

        .online-indicator.offline {
            background-color: #9CA3AF;
        }

        .message-enter {
            opacity: 0;
            transform: translateY(10px);
        }

        .message-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 300ms, transform 300ms;
        }

        #messagesContainer::-webkit-scrollbar {
            width: 6px;
        }

        #messagesContainer::-webkit-scrollbar-track {
            background: transparent;
        }

        #messagesContainer::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 3px;
        }

        #messagesContainer::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        .dark #messagesContainer::-webkit-scrollbar-thumb {
            background: #475569;
        }

        .dark #messagesContainer::-webkit-scrollbar-thumb:hover {
            background: #64748B;
        }
    </style>
</div>
