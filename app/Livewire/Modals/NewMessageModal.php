<?php

namespace App\Livewire\Modals;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Rule;
use Livewire\Component;

class NewMessageModal extends Component
{
    #[Rule('required|exists:users,id')]
    public $recipientId = '';

    #[Rule('required|string|max:5000')]
    public $initialMessage = '';

    #[Rule('nullable|exists:orders,id')]
    public $relatedOrderId = '';

    public $admins = [];
    public $userOrders = [];
    public $showModal = false;

    protected $listeners = [
        'openNewMessageModal' => 'openModal',
        'closeModal' => 'closeModal'
    ];

    public function mount()
    {
        $this->loadAdmins();
        $this->loadUserOrders();
    }

    public function loadAdmins()
    {
        try {
            $userId = Auth::id();
            $this->admins = User::where('role', 'admin')
                ->where('id', '!=', $userId)
                ->where('status', 'active')
                ->get()
                ->map(function ($admin) {
                    return [
                        'id' => $admin->id,
                        'name' => $admin->name,
                        'email' => $admin->email,
                        'initials' => $admin->initials(),
                        'avatar_color' => $admin->avatar_color,
                        'is_online' => $admin->isOnline(),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading admins: ' . $e->getMessage());
            $this->admins = [];
        }
    }

    public function loadUserOrders()
    {
        try {
            $userId = Auth::id();
            $this->userOrders = Order::where('user_id', $userId)
                ->with('package')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'package_name' => $order->package->name ?? 'Unknown Package',
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('d/m/Y'),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading user orders: ' . $e->getMessage());
            $this->userOrders = [];
        }
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->loadAdmins();
        $this->loadUserOrders();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['recipientId', 'initialMessage', 'relatedOrderId']);
    }

    public function createConversation()
    {
        Log::info('=== CREATE CONVERSATION MODAL ===');
        Log::info('Form data:', [
            'recipientId' => $this->recipientId,
            'initialMessage' => $this->initialMessage ? substr($this->initialMessage, 0, 50) . '...' : 'empty',
            'relatedOrderId' => $this->relatedOrderId,
            'user_id' => Auth::id()
        ]);

        // Validate input
        $this->validate();

        try {
            DB::beginTransaction();

            $userId = Auth::id();

            // Validate recipient is admin
            $recipient = User::find($this->recipientId);
            if (!$recipient || $recipient->role !== 'admin') {
                $this->addError('recipientId', 'Penerima harus admin');
                return;
            }

            Log::info('Creating conversation between:', [
                'user_id' => $userId,
                'recipient_id' => $this->recipientId,
                'recipient_name' => $recipient->name
            ]);

            // Check if conversation already exists
            $conversation = Conversation::where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->where('user2_id', $this->recipientId);
            })->orWhere(function($query) use ($userId) {
                $query->where('user1_id', $this->recipientId)
                      ->where('user2_id', $userId);
            })->first();

            if (!$conversation) {
                // Create new conversation
                $conversation = Conversation::create([
                    'user1_id' => $userId,
                    'user2_id' => $this->recipientId,
                    'order_id' => $this->relatedOrderId,
                    'last_message_at' => now(),
                ]);

                Log::info('New conversation created:', [
                    'conversation_id' => $conversation->id,
                    'user1_id' => $conversation->user1_id,
                    'user2_id' => $conversation->user2_id
                ]);
            } else {
                Log::info('Using existing conversation:', [
                    'conversation_id' => $conversation->id
                ]);

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

            Log::info('Initial message created:', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id
            ]);

            // Update last_message_at
            $conversation->update(['last_message_at' => now()]);

            // Reset form
            $this->reset(['recipientId', 'initialMessage', 'relatedOrderId']);

            // Commit transaction
            DB::commit();

            Log::info('Transaction committed successfully');

            // Dispatch success event to parent component
            $this->dispatch('conversation-created',
                conversationId: $conversation->id,
                message: 'Percakapan baru berhasil dibuat'
            );

            // Close modal
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create conversation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            $this->addError('initialMessage', 'Gagal membuat percakapan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modals.new-message-modal');
    }
}
