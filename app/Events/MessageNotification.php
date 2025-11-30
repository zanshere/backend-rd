<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $notificationData;

    public function __construct($userId, Message $message)
    {
        $this->userId = $userId;
        $this->notificationData = $this->prepareNotificationData($message);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'notification.created';
    }

    public function broadcastWith()
    {
        return $this->notificationData;
    }

    private function prepareNotificationData(Message $message)
    {
        $conversation = $message->conversation;
        $sender = $message->sender;
        $recipient = $conversation->user1_id == $sender->id ? $conversation->user2 : $conversation->user1;

        return [
            'id' => uniqid(),
            'type' => 'message',
            'title' => 'Pesan Baru dari ' . $sender->name,
            'message' => \Illuminate\Support\Str::limit($message->content, 100),
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'sender_initials' => $sender->initials(),
            'created_at' => now()->toISOString(),
            'data' => [
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
                'sender' => [
                    'id' => $sender->id,
                    'name' => $sender->name,
                    'initials' => $sender->initials(),
                ],
                'preview' => \Illuminate\Support\Str::limit($message->content, 100),
            ]
        ];
    }
}   
