<?php

namespace App\Livewire\Common;

use App\Events\MessageRead;
use App\Events\MessageNotification;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;

class Messages extends Component
{
    use WithFileUploads;

    public $selectedConversationId = null;
    public $showNewMessageModal = false;
    public $newMessage = '';
    public $attachments = [];
    public $recipientId = '';
    public $relatedOrderId = '';
    public $initialMessage = '';
    public $onlineUsers = [];

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
        'notificationReceived' => 'handleNotification',
    ];

    public function mount()
    {
        $this->loadData();

        // Jika ada conversation ID di query string, gunakan itu
        if ($this->selectedConversationId) {
            $this->validateConversationAccess($this->selectedConversationId);
        }
    }

    // Method untuk memuat semua data
    public function loadData()
    {
        $this->loadConversations();
        $this->loadSelectedConversation();
        $this->loadMessages();
        $this->loadAdmins();
        $this->loadUserOrders();
    }

    // Method untuk refresh data
    public function refreshData()
    {
        $this->loadData();
    }

    // Method untuk memuat conversations
    public function loadConversations()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['lastMessage', 'user1', 'user2'])
            ->where(function($query) use ($userId) {
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
                ? ($conversation->user2 ?? null)
                : ($conversation->user1 ?? null);

            return $conversation;
        })->filter(function ($conversation) {
            return !is_null($conversation->other_user);
        })->toArray();
    }

    // Method untuk memuat selected conversation
    public function loadSelectedConversation()
    {
        if (!$this->selectedConversationId) {
            $this->currentConversation = null;
            return;
        }

        $userId = Auth::id();
        $conversation = Conversation::with(['user1', 'user2', 'order'])
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->find($this->selectedConversationId);

        if ($conversation) {
            $conversation->other_user = $conversation->user1_id == $userId
                ? ($conversation->user2 ?? null)
                : ($conversation->user1 ?? null);

            $this->currentConversation = $conversation;
        } else {
            $this->currentConversation = null;
        }
    }

    // METHOD messages() YANG DIPANGGIL OLEH LIVEWIRE - INI YANG DIBUTUHKAN
    public function messages()
    {
        return $this->loadMessagesData();
    }

    // Method untuk memuat messages
    public function loadMessages()
    {
        $this->messagesList = $this->loadMessagesData();
    }

    // Method internal untuk memuat data messages
    private function loadMessagesData()
    {
        if (!$this->selectedConversationId) {
            return [];
        }

        return Message::with(['sender', 'attachments'])
            ->where('conversation_id', $this->selectedConversationId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    // Method untuk memuat admins
    public function loadAdmins()
    {
        $userId = Auth::id();
        $this->adminsList = User::where('role', 'admin')
            ->where('id', '!=', $userId)
            ->where('status', 'active')
            ->get()
            ->toArray();
    }

    // Method untuk memuat user orders
    public function loadUserOrders()
    {
        $userId = Auth::id();
        $this->userOrdersList = Order::where('user_id', $userId)
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getListeners()
    {
        $userId = Auth::id();

        $listeners = [
            'refreshConversations' => 'refreshData',
            'refreshMessages' => 'refreshData',
            "echo-private:notifications.{$userId},notification.created" => 'handleNotification',
        ];

        if ($this->selectedConversationId) {
            $listeners["echo-presence:conversation.{$this->selectedConversationId},message.sent"] = 'handleNewMessage';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},message.read"] = 'handleMessageRead';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},here"] = 'handleUsersHere';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},joining"] = 'handleUserJoining';
            $listeners["echo-presence:conversation.{$this->selectedConversationId},leaving"] = 'handleUserLeaving';
        }

        return $listeners;
    }

    public function handleNewMessage($data)
    {
        // Refresh messages when new message is received
        $this->loadMessages();
        $this->loadConversations();

        // Jika pesan bukan dari user saat ini, mark as read
        if ($data['message']['sender_id'] !== Auth::id()) {
            $this->markAsRead();
        }

        // Show notification if message is from other user
        if ($data['message']['sender_id'] !== Auth::id()) {
            $this->dispatch('show-info', 'Pesan baru: ' . \Illuminate\Support\Str::limit($data['message']['content'], 50));
        }

        $this->dispatch('messages-updated');
        $this->dispatch('conversations-updated');
    }

    public function handleNotification($data)
    {
        // Handle incoming notification
        if (isset($data['type']) && $data['type'] === 'message') {
            $this->dispatch('show-info', $data['title'] . ': ' . $data['message']);

            // Refresh conversations if notification is for a different conversation
            if (isset($data['data']['conversation_id']) && $data['data']['conversation_id'] != $this->selectedConversationId) {
                $this->loadConversations();
                $this->dispatch('conversations-updated');
            }
        }
    }

    public function handleMessageRead($data)
    {
        // Refresh messages to update read status
        $this->loadMessages();
        $this->loadConversations();

        $this->dispatch('messages-updated');
        $this->dispatch('conversations-updated');
    }

    public function handleUsersHere($users)
    {
        // Handle users currently in the channel
        $this->onlineUsers = $users;
        $this->dispatch('users-updated', users: $users);
    }

    public function handleUserJoining($user)
    {
        // Handle user joining
        if (!collect($this->onlineUsers)->contains('id', $user['id'])) {
            $this->onlineUsers[] = $user;
        }
        $this->dispatch('user-joined', user: $user);
    }

    public function handleUserLeaving($user)
    {
        // Handle user leaving
        $this->onlineUsers = collect($this->onlineUsers)->reject(function ($u) use ($user) {
            return $u['id'] === $user['id'];
        })->values()->toArray();
        $this->dispatch('user-left', user: $user);
    }

    public function selectConversation($conversationId)
    {
        if ($this->validateConversationAccess($conversationId)) {
            $this->selectedConversationId = $conversationId;
            $this->loadSelectedConversation();
            $this->loadMessages();
            $this->markAsRead();
            $this->newMessage = '';
            $this->attachments = [];

            // Dispatch event untuk update Echo listeners
            $this->dispatch('conversation-changed', conversationId: $conversationId);
        }
    }

    protected function validateConversationAccess($conversationId)
    {
        $userId = Auth::id();
        $conversation = Conversation::where(function($query) use ($userId) {
            $query->where('user1_id', $userId)
                  ->orWhere('user2_id', $userId);
        })->find($conversationId);

        if (!$conversation) {
            $this->selectedConversationId = null;
            $this->currentConversation = null;
            $this->messagesList = [];
            $this->dispatch('show-error', 'Percakapan tidak ditemukan atau tidak memiliki akses.');
            return false;
        }

        return true;
    }

    public function markAsRead()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $userId = Auth::id();

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

            // Broadcast event bahwa pesan telah dibaca
            broadcast(new MessageRead($this->selectedConversationId, $userId, $messageIds));

            $this->loadConversations();
            $this->loadMessages();

            $this->dispatch('conversations-updated');
            $this->dispatch('messages-updated');
        }
    }

    public function sendMessage()
    {
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
                $message = Message::create([
                    'conversation_id' => $this->selectedConversationId,
                    'sender_id' => $userId,
                    'content' => $this->newMessage ?: '[Attachment]',
                ]);

                // Handle attachments
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
                    }
                }

                // Update conversation last message time
                Conversation::where('id', $this->selectedConversationId)
                    ->update(['last_message_at' => now()]);

                // Reset form
                $this->newMessage = '';
                $this->attachments = [];

                // Reload data
                $this->loadConversations();
                $this->loadMessages();
            });

            $this->dispatch('show-success', 'Pesan berhasil dikirim.');
            $this->dispatch('messages-updated');
            $this->dispatch('conversations-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    public function startNewConversation()
    {
        $this->validate([
            'recipientId' => 'required|exists:users,id',
            'initialMessage' => 'required|string|max:5000',
            'relatedOrderId' => 'nullable|exists:orders,id',
        ]);

        try {
            DB::transaction(function () {
                $userId = Auth::id();

                // Check if conversation already exists between these users
                $conversation = Conversation::where(function($query) use ($userId) {
                    $query->where('user1_id', $userId)
                          ->where('user2_id', $this->recipientId);
                })->orWhere(function($query) use ($userId) {
                    $query->where('user1_id', $this->recipientId)
                          ->where('user2_id', $userId);
                })->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'user1_id' => $userId,
                        'user2_id' => $this->recipientId,
                        'order_id' => $this->relatedOrderId,
                        'last_message_at' => now(),
                    ]);
                } else {
                    // Update order_id if provided
                    if ($this->relatedOrderId) {
                        $conversation->update(['order_id' => $this->relatedOrderId]);
                    }
                }

                // Create first message
                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $userId,
                    'content' => $this->initialMessage,
                ]);

                // Update conversation last message time
                $conversation->update(['last_message_at' => now()]);

                // Reset form and close modal
                $this->reset(['recipientId', 'relatedOrderId', 'initialMessage', 'showNewMessageModal']);

                // Select the new conversation
                $this->selectConversation($conversation->id);

                $this->dispatch('show-success', 'Percakapan baru berhasil dibuat.');
            });

        } catch (\Exception $e) {
            $this->dispatch('show-error', 'Gagal membuat percakapan: ' . $e->getMessage());
        }
    }

    public function removeAttachment($index)
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = MessageAttachment::findOrFail($attachmentId);

        // Check if user has access to this attachment
        $message = $attachment->message;
        $conversation = $message->conversation;
        $userId = Auth::id();

        if ($conversation->user1_id != $userId && $conversation->user2_id != $userId) {
            abort(403, 'Unauthorized access to this attachment.');
        }

        return Storage::disk('public')->download($attachment->path, $attachment->original_name);
    }

    public function deleteConversation($conversationId)
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);
            $userId = Auth::id();

            // Check if user is part of this conversation
            if ($conversation->user1_id != $userId && $conversation->user2_id != $userId) {
                abort(403, 'Unauthorized action.');
            }

            $conversation->delete();

            // Reset selected conversation if it was deleted
            if ($this->selectedConversationId == $conversationId) {
                $this->selectedConversationId = null;
                $this->currentConversation = null;
                $this->messagesList = [];
            }

            $this->loadConversations();

            $this->dispatch('show-success', 'Percakapan berhasil dihapus.');
            $this->dispatch('conversations-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-error', 'Gagal menghapus percakapan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.common.messages', [
            'conversations' => $this->conversationsList,
            'selectedConversation' => $this->currentConversation,
            'messages' => $this->messagesList,
            'admins' => $this->adminsList,
            'userOrders' => $this->userOrdersList,
        ]);
    }
}
