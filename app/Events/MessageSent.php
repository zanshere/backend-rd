<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender', 'attachments']);
    }

    public function broadcastOn()
    {
        return new PresenceChannel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'sender_id' => $this->message->sender_id,
                'conversation_id' => $this->message->conversation_id,
                'created_at' => $this->message->created_at->toISOString(),
                'read_at' => $this->message->read_at?->toISOString(),
                'sender' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'email' => $this->message->sender->email,
                    'initials' => $this->message->sender->initials(),
                ],
                'attachments' => $this->message->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'original_name' => $attachment->original_name,
                        'path' => $attachment->path,
                        'size' => $attachment->size,
                        'mime_type' => $attachment->mime_type,
                    ];
                })->toArray(),
            ],
        ];
    }
}
