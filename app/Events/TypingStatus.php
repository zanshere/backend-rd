<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversationId;
    public $userId;
    public $isTyping;
    public $user;

    public function __construct($conversationId, $userId, $isTyping)
    {
        $this->conversationId = $conversationId;
        $this->userId = $userId;
        $this->isTyping = $isTyping;
        $this->user = \App\Models\User::find($userId);
    }

    public function broadcastOn()
    {
        return new PresenceChannel('conversation.' . $this->conversationId);
    }

    public function broadcastAs()
    {
        return 'typing.status';
    }

    public function broadcastWith()
    {
        return [
            'conversation_id' => $this->conversationId,
            'user_id' => $this->userId,
            'is_typing' => $this->isTyping,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'initials' => $this->user->initials(),
                'avatar_color' => $this->user->avatar_color,
            ] : null,
        ];
    }

    /**
     * Determine if this event should be queued.
     */
    public function shouldQueue(): bool
    {
        return true;
    }

    /**
     * Get the queue connection that should be used.
     */
    public function connection(): string
    {
        return 'database';
    }

    /**
     * Get the queue that should be used.
     */
    public function queue(): string
    {
        return 'broadcasts';
    }
}
