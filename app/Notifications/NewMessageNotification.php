<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $sender = $this->message->sender;
        $conversation = $this->message->conversation;

        return [
            'id' => uniqid(),
            'type' => 'message',
            'title' => 'Pesan Baru dari ' . $sender->name,
            'message' => \Illuminate\Support\Str::limit($this->message->content, 100),
            'icon' => 'message-square',
            'conversation_id' => $conversation->id,
            'message_id' => $this->message->id,
            'sender' => [
                'id' => $sender->id,
                'name' => $sender->name,
                'initials' => $sender->initials(),
            ],
            'preview' => \Illuminate\Support\Str::limit($this->message->content, 100),
            'created_at' => now()->toISOString(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
