<?php

namespace App\Livewire\Common;

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
use Livewire\WithPagination;

class Messages extends Component
{
    use WithPagination, WithFileUploads;

    public $selectedConversation = null;
    public $showNewMessageModal = false;
    public $newMessage = '';
    public $attachments = [];
    public $recipientId = '';
    public $relatedOrderId = '';
    public $initialMessage = '';

    protected $listeners = [
        'refreshConversations' => '$refresh',
        'refreshMessages' => '$refresh',
    ];

    public function mount()
    {
        $userId = Auth::id();
        if (!$this->selectedConversation && $this->conversations->count() > 0) {
            $this->selectConversation($this->conversations->first()->id);
        }
    }

    public function getConversationsProperty()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['lastMessage', 'user1', 'user2'])
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Add unread count and other user manually for each conversation
        return $conversations->map(function ($conversation) use ($userId) {
            $conversation->unread_count = $conversation->messages()
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->count();
            $conversation->other_user = $conversation->user1_id == $userId ? $conversation->user2 : $conversation->user1;
            return $conversation;
        });
    }

    public function getMessagesProperty()
    {
        if (!$this->selectedConversation) {
            return collect();
        }

        return Message::with(['sender', 'attachments'])
            ->where('conversation_id', $this->selectedConversation->id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getAdminsProperty()
    {
        $userId = Auth::id();
        return User::where('role', 'admin')
            ->where('id', '!=', $userId)
            ->where('status', 'active')
            ->get();
    }

    public function getUserOrdersProperty()
    {
        $userId = Auth::id();
        return Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selectConversation($conversationId)
    {
        $userId = Auth::id();
        $this->selectedConversation = Conversation::with(['user1', 'user2', 'order'])
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->findOrFail($conversationId);

        // Set other user manually
        $this->selectedConversation->other_user = $this->selectedConversation->user1_id == $userId
            ? $this->selectedConversation->user2
            : $this->selectedConversation->user1;

        $this->markAsRead();
        $this->newMessage = '';
        $this->attachments = [];

        $this->dispatch('conversationSelected');
    }

    public function markAsRead()
    {
        if (!$this->selectedConversation) {
            return;
        }

        $userId = Auth::id();
        Message::where('conversation_id', $this->selectedConversation->id)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dispatch('refreshConversations');
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required_without:attachments|string|max:5000',
            'attachments.*' => 'file|max:10240', // 10MB max
        ]);

        if (empty($this->newMessage) && empty($this->attachments)) {
            session()->flash('error', 'Pesan atau attachment harus diisi.');
            return;
        }

        try {
            DB::transaction(function () {
                $userId = Auth::id();
                $message = Message::create([
                    'conversation_id' => $this->selectedConversation->id,
                    'sender_id' => $userId,
                    'content' => $this->newMessage ?: '[Attachment]',
                    'created_at' => now(),
                    'updated_at' => now(),
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
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Update conversation last message time
                $this->selectedConversation->update([
                    'last_message_at' => now(),
                    'updated_at' => now(),
                ]);

                // Reset form
                $this->newMessage = '';
                $this->attachments = [];

                session()->flash('message', 'Pesan berhasil dikirim.');
            });

            $this->dispatch('refreshMessages');
            $this->dispatch('refreshConversations');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim pesan: ' . $e->getMessage());
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Create first message
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $userId,
                    'content' => $this->initialMessage,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update conversation last message time
                $conversation->update([
                    'last_message_at' => now(),
                    'updated_at' => now(),
                ]);

                // Reset form and close modal
                $this->reset(['recipientId', 'relatedOrderId', 'initialMessage', 'showNewMessageModal']);

                // Select the new conversation
                $this->selectConversation($conversation->id);

                session()->flash('message', 'Percakapan baru berhasil dibuat.');
            });

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat percakapan: ' . $e->getMessage());
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
            if ($this->selectedConversation && $this->selectedConversation->id == $conversationId) {
                $this->selectedConversation = null;
            }

            session()->flash('message', 'Percakapan berhasil dihapus.');
            $this->dispatch('refreshConversations');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus percakapan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.common.messages', [
            'conversations' => $this->conversations,
            'messages' => $this->messages,
            'admins' => $this->admins,
            'userOrders' => $this->userOrders,
        ]);
    }
}
