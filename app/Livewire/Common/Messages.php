<?php

namespace App\Livewire\Common;

use App\Events\MessageRead as EventsMessageRead;
use App\Events\TypingStatus;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Messages extends Component
{
    use WithFileUploads, WithPagination;

    public $selectedConversationId = null;
    public $newMessage = '';
    public $attachments = [];
    public $onlineUsers = [];
    public $typingUsers = [];
    public $isTyping = false;
    public $typingTimeout;

    // Public properties untuk data yang akan diakses di view
    public $conversationsList = [];
    public $currentConversation = null;
    public $messagesList = [];
    public $adminsList = [];
    public $userOrdersList = [];

    protected $queryString = [
        'selectedConversationId' => ['except' => '', 'as' => 'conversation']
    ];

    protected $listeners = [
        'refreshConversations' => 'refreshData',
        'refreshMessages' => 'refreshData',
        'page-became-visible' => 'refreshData',
        'start-typing' => 'startTypingIndicator',
        'stop-typing' => 'stopTypingIndicator',
        'conversation-created' => 'handleConversationCreated',
        'conversation-created-error' => 'handleConversationError',
        'echo-private:notifications.{userId},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'handleBroadcastNotification',
    ];

    public function mount()
    {
        Log::info('Messages component mounted', ['user_id' => Auth::id()]);
        $this->loadData();

        if ($this->selectedConversationId) {
            $this->validateConversationAccess($this->selectedConversationId);
        }
    }

    public function loadData()
    {
        try {
            $this->loadConversations();
            $this->loadSelectedConversation();
            $this->loadMessages();
            $this->loadAdmins();
            $this->loadUserOrders();

            Log::info('Data loaded successfully', [
                'conversations_count' => count($this->conversationsList),
                'messages_count' => count($this->messagesList),
                'admins_count' => count($this->adminsList),
                'orders_count' => count($this->userOrdersList)
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading data: ' . $e->getMessage());
        }
    }

    public function refreshData()
    {
        $this->loadData();
        $this->dispatch('data-refreshed');
    }

    public function loadConversations()
    {
        $userId = Auth::id();

        try {
            $conversations = Conversation::with(['lastMessage.sender', 'user1', 'user2'])
                ->where(function ($query) use ($userId) {
                    $query->where('user1_id', $userId)
                        ->orWhere('user2_id', $userId);
                })
                ->orderBy('last_message_at', 'desc')
                ->get();

            $this->conversationsList = $conversations->map(function ($conversation) use ($userId) {
                $conversation->unread_count = $conversation->messages()
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                $conversation->other_user = $conversation->user1_id == $userId
                    ? $conversation->user2
                    : $conversation->user1;

                // Return sebagai array untuk serialization
                return [
                    'id' => $conversation->id,
                    'user1_id' => $conversation->user1_id,
                    'user2_id' => $conversation->user2_id,
                    'order_id' => $conversation->order_id,
                    'title' => $conversation->title,
                    'last_message_at' => $conversation->last_message_at,
                    'created_at' => $conversation->created_at,
                    'updated_at' => $conversation->updated_at,
                    'unread_count' => $conversation->unread_count,
                    'other_user' => $conversation->other_user ? [
                        'id' => $conversation->other_user->id,
                        'name' => $conversation->other_user->name,
                        'email' => $conversation->other_user->email,
                        'phone' => $conversation->other_user->phone,
                        'role' => $conversation->other_user->role,
                        'status' => $conversation->other_user->status,
                        'company_name' => $conversation->other_user->company_name,
                        'company_address' => $conversation->other_user->company_address,
                        'initials' => $conversation->other_user->initials(),
                        'is_online' => $conversation->other_user->isOnline(),
                        'last_seen' => $conversation->other_user->last_activity_at?->diffForHumans() ?? $conversation->other_user->last_login_at?->diffForHumans(),
                        'avatar_color' => $conversation->other_user->avatar_color,
                        'avatar_url' => $conversation->other_user->avatar_url,
                        'last_login_at' => $conversation->other_user->last_login_at,
                        'last_activity_at' => $conversation->other_user->last_activity_at,
                    ] : null,
                    'last_message' => $conversation->lastMessage ? [
                        'id' => $conversation->lastMessage->id,
                        'content' => $conversation->lastMessage->content,
                        'sender_id' => $conversation->lastMessage->sender_id,
                        'conversation_id' => $conversation->lastMessage->conversation_id,
                        'read_at' => $conversation->lastMessage->read_at,
                        'created_at' => $conversation->lastMessage->created_at,
                        'updated_at' => $conversation->lastMessage->updated_at,
                    ] : null,
                ];
            })->filter(function ($conversation) {
                return !is_null($conversation['other_user']);
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading conversations: ' . $e->getMessage());
            $this->conversationsList = [];
        }
    }

    public function loadSelectedConversation()
    {
        if (!$this->selectedConversationId) {
            $this->currentConversation = null;
            return;
        }

        $userId = Auth::id();

        try {
            $conversation = Conversation::with(['user1', 'user2', 'order', 'order.package'])
                ->where(function ($query) use ($userId) {
                    $query->where('user1_id', $userId)
                        ->orWhere('user2_id', $userId);
                })
                ->find($this->selectedConversationId);

            if ($conversation) {
                $conversation->other_user = $conversation->user1_id == $userId
                    ? $conversation->user2
                    : $conversation->user1;

                // Konversi ke array untuk serialization
                $this->currentConversation = [
                    'id' => $conversation->id,
                    'user1_id' => $conversation->user1_id,
                    'user2_id' => $conversation->user2_id,
                    'order_id' => $conversation->order_id,
                    'title' => $conversation->title,
                    'last_message_at' => $conversation->last_message_at,
                    'created_at' => $conversation->created_at,
                    'updated_at' => $conversation->updated_at,
                    'other_user' => $conversation->other_user ? [
                        'id' => $conversation->other_user->id,
                        'name' => $conversation->other_user->name,
                        'email' => $conversation->other_user->email,
                        'phone' => $conversation->other_user->phone,
                        'role' => $conversation->other_user->role,
                        'status' => $conversation->other_user->status,
                        'company_name' => $conversation->other_user->company_name,
                        'company_address' => $conversation->other_user->company_address,
                        'initials' => $conversation->other_user->initials(),
                        'is_online' => $conversation->other_user->isOnline(),
                        'last_seen' => $conversation->other_user->last_activity_at?->diffForHumans() ?? $conversation->other_user->last_login_at?->diffForHumans(),
                        'avatar_color' => $conversation->other_user->avatar_color,
                        'avatar_url' => $conversation->other_user->avatar_url,
                        'last_login_at' => $conversation->other_user->last_login_at,
                        'last_activity_at' => $conversation->other_user->last_activity_at,
                    ] : null,
                ];
            } else {
                $this->currentConversation = null;
                Log::warning('Conversation not found or access denied', ['conversation_id' => $this->selectedConversationId]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading selected conversation: ' . $e->getMessage());
            $this->currentConversation = null;
        }
    }

    public function loadMessages()
    {
        $this->messagesList = $this->loadMessagesData();
    }

    private function loadMessagesData()
    {
        if (!$this->selectedConversationId) {
            return [];
        }

        try {
            return Message::with(['sender', 'attachments'])
                ->where('conversation_id', $this->selectedConversationId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'conversation_id' => $message->conversation_id,
                        'sender_id' => $message->sender_id,
                        'content' => $message->content,
                        'read_at' => $message->read_at,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                        'delivery_status' => $message->delivery_status,
                        'formatted_time' => $message->formatted_time,
                        'is_read' => $message->is_read,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading messages: ' . $e->getMessage());
            return [];
        }
    }

    public function loadAdmins()
    {
        $userId = Auth::id();

        try {
            $admins = User::where('role', 'admin')
                ->where('id', '!=', $userId)
                ->where('status', 'active')
                ->get();

            $this->adminsList = $admins->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'phone' => $admin->phone,
                    'role' => $admin->role,
                    'status' => $admin->status,
                    'company_name' => $admin->company_name,
                    'company_address' => $admin->company_address,
                    'initials' => $admin->initials(),
                    'is_online' => $admin->isOnline(),
                    'last_seen' => $admin->last_activity_at?->diffForHumans() ?? $admin->last_login_at?->diffForHumans(),
                    'avatar_color' => $admin->avatar_color,
                    'avatar_url' => $admin->avatar_url,
                    'last_login_at' => $admin->last_login_at,
                    'last_activity_at' => $admin->last_activity_at,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading admins: ' . $e->getMessage());
            $this->adminsList = [];
        }
    }

    public function loadUserOrders()
    {
        $userId = Auth::id();

        try {
            $orders = Order::where('user_id', $userId)
                ->with('package')
                ->orderBy('created_at', 'desc')
                ->get();

            $this->userOrdersList = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'package_id' => $order->package_id,
                    'status' => $order->status,
                    'total_price' => $order->total_price,
                    'notes' => $order->notes,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'package' => $order->package ? [
                        'id' => $order->package->id,
                        'name' => $order->package->name,
                        'description' => $order->package->description,
                        'price' => $order->package->price,
                        'duration' => $order->package->duration,
                        'features' => $order->package->features,
                    ] : null,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading user orders: ' . $e->getMessage());
            $this->userOrdersList = [];
        }
    }

    public function getListeners()
    {
        $userId = Auth::id();

        $listeners = [
            'refreshConversations' => 'refreshData',
            'refreshMessages' => 'refreshData',
            'page-became-visible' => 'refreshData',
            "echo-private:notifications.{$userId},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'handleBroadcastNotification',
            "echo-private:notifications.{$userId},App\\Events\\MessageNotification" => 'handleMessageNotification',
        ];

        if ($this->selectedConversationId) {
            $listeners["echo-presence:conversation.{$this->selectedConversationId},here"] = 'handleUsersHere';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},joining"] = 'handleUserJoining';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},leaving"] = 'handleUserLeaving';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},message.sent"] = 'handleNewMessage';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},message.read"] = 'handleMessageRead';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},typing.status"] = 'handleTypingStatus';
        }

        // Listen for online status changes
        $listeners["echo-presence:online-users,here"] = 'handleOnlineUsersHere';
        $listeners["echo-presence:online-users,joining"] = 'handleUserJoiningOnline';
        $listeners["echo-presence:online-users,leaving"] = 'handleUserLeavingOnline';
        $listeners["echo-presence:online-users,user.online.status"] = 'handleUserOnlineStatusChanged';

        return $listeners;
    }

    public function handleNewMessage($data)
    {
        Log::info('New message received', ['data' => $data]);

        $this->loadMessages();
        $this->loadConversations();

        if ($data['message']['sender_id'] !== Auth::id()) {
            $this->markAsRead();

            $this->dispatch(
                'show-info',
                'Pesan baru: ' . \Illuminate\Support\Str::limit($data['message']['content'], 50)
            );
        }

        $this->dispatch('messages-updated');
        $this->dispatch('conversations-updated');
        $this->dispatch('scroll-to-bottom');
    }

    public function handleMessageNotification($data)
    {
        Log::info('Message notification received', ['data' => $data]);

        // Update notifications badge
        $this->dispatch('update-notifications-count');

        // Show notification if not in conversation
        if (!isset($data['data']['conversation_id']) || $data['data']['conversation_id'] != $this->selectedConversationId) {
            $this->dispatch('show-notification', [
                'title' => $data['title'] ?? 'Pesan Baru',
                'message' => $data['message'] ?? '',
                'type' => 'info',
                'data' => $data['data'] ?? []
            ]);
        }
    }

    public function handleBroadcastNotification($event)
    {
        Log::info('Broadcast notification received', ['event' => $event]);

        if (isset($event['type']) && $event['type'] === 'message') {
            $this->dispatch('show-notification', [
                'title' => $event['title'] ?? 'Notifikasi',
                'message' => $event['message'] ?? '',
                'type' => 'info'
            ]);

            if (isset($event['conversation_id']) && $event['conversation_id'] != $this->selectedConversationId) {
                $this->loadConversations();
                $this->dispatch('conversations-updated');
            }
        }
    }

    public function handleMessageRead($data)
    {
        Log::info('Message read event', ['data' => $data]);

        $this->loadMessages();
        $this->loadConversations();

        $this->dispatch('messages-updated');
        $this->dispatch('conversations-updated');
    }

    public function handleTypingStatus($data)
    {
        Log::info('Typing status event', ['data' => $data]);

        $userId = $data['user_id'];

        if ($userId !== Auth::id()) {
            if ($data['is_typing']) {
                $this->typingUsers[$userId] = $data['user'];
                $this->dispatch('user-typing', $data);
            } else {
                unset($this->typingUsers[$userId]);
                $this->dispatch('user-stop-typing', $data);
            }
        }
    }

    public function handleUsersHere($users)
    {
        Log::info('Users here in conversation', ['users_count' => count($users)]);

        $this->onlineUsers = $users;
        $this->dispatch('users-updated', users: $users);
    }

    public function handleUserJoining($user)
    {
        Log::info('User joining conversation', ['user' => $user]);

        if (!collect($this->onlineUsers)->contains('id', $user['id'])) {
            $this->onlineUsers[] = $user;
        }
        $this->dispatch('user-joined', user: $user);
    }

    public function handleUserLeaving($user)
    {
        Log::info('User leaving conversation', ['user' => $user]);

        $this->onlineUsers = collect($this->onlineUsers)->reject(function ($u) use ($user) {
            return $u['id'] === $user['id'];
        })->values()->toArray();
        $this->dispatch('user-left', user: $user);
    }

    public function handleOnlineUsersHere($users)
    {
        Log::info('Online users here', ['users_count' => count($users)]);

        $this->onlineUsers = $users;
        $this->dispatch('online-users-updated', users: $users);
    }

    public function handleUserJoiningOnline($user)
    {
        Log::info('User joining online', ['user' => $user]);

        if (!collect($this->onlineUsers)->contains('id', $user['id'])) {
            $this->onlineUsers[] = $user;
        }
        $this->dispatch('user-online-joined', user: $user);
    }

    public function handleUserLeavingOnline($user)
    {
        Log::info('User leaving online', ['user' => $user]);

        $this->onlineUsers = collect($this->onlineUsers)->reject(function ($u) use ($user) {
            return $u['id'] === $user['id'];
        })->values()->toArray();
        $this->dispatch('user-online-left', user: $user);
    }

    public function handleUserOnlineStatusChanged($data)
    {
        Log::info('User online status changed', ['data' => $data]);

        $userId = $data['user_id'];
        $isOnline = $data['is_online'];

        // Update online users list
        if ($isOnline) {
            if (!collect($this->onlineUsers)->contains('id', $userId)) {
                $this->onlineUsers[] = $data['user'];
            }
        } else {
            $this->onlineUsers = collect($this->onlineUsers)->reject(function ($user) use ($userId) {
                return $user['id'] === $userId;
            })->values()->toArray();
        }

        $this->dispatch('user-online-status-changed', $data);
    }

    public function selectConversation($conversationId)
    {
        Log::info('selectConversation called', ['conversation_id' => $conversationId]);

        if ($this->validateConversationAccess($conversationId)) {
            $this->selectedConversationId = $conversationId;
            $this->loadSelectedConversation();
            $this->loadMessages();
            $this->markAsRead();
            $this->newMessage = '';
            $this->attachments = [];
            $this->typingUsers = [];

            $this->dispatch('conversation-changed', conversationId: $conversationId);
            $this->dispatch('scroll-to-bottom');

            Log::info('Conversation selected successfully', ['conversation_id' => $conversationId]);
        } else {
            Log::warning('Conversation access denied', ['conversation_id' => $conversationId]);
            $this->dispatch('show-error', 'Akses ditolak ke percakapan ini.');
        }
    }

    protected function validateConversationAccess($conversationId)
    {
        $userId = Auth::id();

        try {
            $conversation = Conversation::where(function ($query) use ($userId) {
                $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
            })->find($conversationId);

            if (!$conversation) {
                $this->selectedConversationId = null;
                $this->currentConversation = null;
                $this->messagesList = [];
                Log::warning('Conversation not found or access denied', [
                    'user_id' => $userId,
                    'conversation_id' => $conversationId
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error validating conversation access: ' . $e->getMessage());
            return false;
        }
    }

    public function markAsRead()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $userId = Auth::id();

        try {
            $unreadMessages = Message::where('conversation_id', $this->selectedConversationId)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->get();

            if ($unreadMessages->count() > 0) {
                $messageIds = $unreadMessages->pluck('id')->toArray();

                Message::where('conversation_id', $this->selectedConversationId)
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

                broadcast(new EventsMessageRead($this->selectedConversationId, $userId, $messageIds));

                $this->loadConversations();
                $this->loadMessages();

                $this->dispatch('conversations-updated');
                $this->dispatch('messages-updated');

                Log::info('Messages marked as read', [
                    'conversation_id' => $this->selectedConversationId,
                    'message_count' => $unreadMessages->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error marking messages as read: ' . $e->getMessage());
        }
    }

    public function updatedNewMessage()
    {
        // Trigger typing indicator
        if (empty($this->newMessage)) {
            $this->stopTypingIndicator();
        } else {
            $this->startTypingIndicator();
        }
    }

    public function startTypingIndicator()
    {
        if (!$this->selectedConversationId) return;

        if (!$this->isTyping) {
            $this->isTyping = true;

            try {
                broadcast(new TypingStatus(
                    $this->selectedConversationId,
                    Auth::id(),
                    true
                ));

                Log::info('Started typing indicator', ['conversation_id' => $this->selectedConversationId]);
            } catch (\Exception $e) {
                Log::error('Error starting typing indicator: ' . $e->getMessage());
            }
        }
    }

    public function stopTypingIndicator()
    {
        if ($this->isTyping && $this->selectedConversationId) {
            $this->isTyping = false;

            try {
                broadcast(new TypingStatus(
                    $this->selectedConversationId,
                    Auth::id(),
                    false
                ));

                Log::info('Stopped typing indicator', ['conversation_id' => $this->selectedConversationId]);
            } catch (\Exception $e) {
                Log::error('Error stopping typing indicator: ' . $e->getMessage());
            }
        }
    }

    public function sendMessage()
    {
        Log::info('sendMessage called', [
            'conversation_id' => $this->selectedConversationId,
            'message_length' => strlen($this->newMessage),
            'attachments_count' => count($this->attachments)
        ]);

        $this->validate([
            'newMessage' => 'required_without:attachments|string|max:5000',
            'attachments.*' => 'file|max:10240',
        ]);

        if (empty($this->newMessage) && empty($this->attachments)) {
            $this->dispatch('show-error', 'Pesan atau attachment harus diisi.');
            return;
        }

        if (!$this->selectedConversationId) {
            $this->dispatch('show-error', 'Tidak ada percakapan yang dipilih.');
            return;
        }

        try {
            DB::transaction(function () {
                $userId = Auth::id();

                Log::info('Creating message', [
                    'conversation_id' => $this->selectedConversationId,
                    'user_id' => $userId
                ]);

                $message = Message::create([
                    'conversation_id' => $this->selectedConversationId,
                    'sender_id' => $userId,
                    'content' => $this->newMessage ?: '[Attachment]',
                ]);

                Log::info('Message created', ['message_id' => $message->id]);

                if ($this->attachments) {
                    foreach ($this->attachments as $attachment) {
                        $path = $attachment->store('message-attachments', 'public');

                        MessageAttachment::create([
                            'message_id' => $message->id,
                            'filename' => $attachment->hashName(),
                            'original_name' => $attachment->getClientOriginalName(),
                            'mime_type' => $attachment->getMimeType(),
                            'size' => $attachment->getSize(),
                            'path' => $path,
                        ]);

                        Log::info('Attachment saved', [
                            'message_id' => $message->id,
                            'filename' => $attachment->getClientOriginalName()
                        ]);
                    }
                }

                $this->newMessage = '';
                $this->attachments = [];
                $this->stopTypingIndicator();

                $this->loadConversations();
                $this->loadMessages();
            });

            $this->dispatch('show-success', 'Pesan berhasil dikirim.');
            $this->dispatch('messages-updated');
            $this->dispatch('conversations-updated');
            $this->dispatch('scroll-to-bottom');

            Log::info('Message sent successfully');
        } catch (\Exception $e) {
            Log::error('Gagal mengirim pesan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('show-error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    public function openNewMessageModal()
    {
        // Dispatch event ke modal component
        $this->dispatch('openNewMessageModal');
    }

    public function handleConversationCreated($conversationId, $message = '')
    {
        Log::info('Conversation created from modal', [
            'conversation_id' => $conversationId,
            'message' => $message
        ]);

        // Pilih conversation yang baru dibuat
        $this->selectConversation($conversationId);

        // Dispatch close modal event
        $this->dispatch('closeModal');

        // Show success message
        $this->dispatch('show-success', message: $message ?: 'Percakapan baru berhasil dibuat.');

        // Refresh data
        $this->refreshData();
    }

    public function handleConversationError($error)
    {
        Log::error('Conversation creation error from modal:', ['error' => $error]);
        $this->dispatch('show-error', message: $error);
    }

    public function removeAttachment($index)
    {
        if (isset($this->attachments[$index])) {
            Log::info('Removing attachment', ['index' => $index]);
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    public function downloadAttachment($attachmentId)
    {
        try {
            $attachment = MessageAttachment::findOrFail($attachmentId);

            $message = $attachment->message;
            $conversation = $message->conversation;
            $userId = Auth::id();

            if ($conversation->user1_id != $userId && $conversation->user2_id != $userId) {
                abort(403, 'Unauthorized access to this attachment.');
            }

            Log::info('Downloading attachment', [
                'attachment_id' => $attachmentId,
                'filename' => $attachment->original_name
            ]);

            // PERBAIKAN: Gunakan response()->download() yang benar
            return response()->download(
                Storage::disk('public')->path($attachment->path),
                $attachment->original_name
            );
        } catch (\Exception $e) {
            Log::error('Error downloading attachment: ' . $e->getMessage());
            abort(404, 'Attachment not found.');
        }
    }

    public function deleteConversation($conversationId)
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);
            $userId = Auth::id();

            if ($conversation->user1_id != $userId && $conversation->user2_id != $userId) {
                abort(403, 'Unauthorized action.');
            }

            $conversation->delete();

            if ($this->selectedConversationId == $conversationId) {
                $this->selectedConversationId = null;
                $this->currentConversation = null;
                $this->messagesList = [];
            }

            $this->loadConversations();

            $this->dispatch('show-success', 'Percakapan berhasil dihapus.');
            $this->dispatch('conversations-updated');

            Log::info('Conversation deleted', ['conversation_id' => $conversationId]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus percakapan: ' . $e->getMessage());
            $this->dispatch('show-error', 'Gagal menghapus percakapan: ' . $e->getMessage());
        }
    }

    /**
     * Convert component to JSON for Livewire serialization
     *
     * @return string
     */
    public function toJSON()
    {
        return json_encode([
            'selectedConversationId' => $this->selectedConversationId,
            'newMessage' => $this->newMessage,
            'isTyping' => $this->isTyping,
            'conversationsList' => $this->conversationsList,
            'currentConversation' => $this->currentConversation,
            'messagesList' => $this->messagesList,
            'adminsList' => $this->adminsList,
            'userOrdersList' => $this->userOrdersList,
            'onlineUsers' => $this->onlineUsers,
            'typingUsers' => $this->typingUsers,
        ], JSON_PRETTY_PRINT);
    }

    public function render()
    {
        Log::info('Messages component rendering', [
            'conversations_count' => count($this->conversationsList),
            'selected_conversation_id' => $this->selectedConversationId
        ]);

        return view('livewire.common.messages');
    }
}
