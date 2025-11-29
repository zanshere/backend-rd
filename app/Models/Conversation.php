<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'order_id',
        'title',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the first user in the conversation
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user in the conversation
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get the order associated with the conversation
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get all messages in the conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    /**
     * Get the last message in the conversation
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get the other user in the conversation relative to given user ID
     */
    public function otherUser($userId = null)
    {
        if (!$userId) {
            return null;
        }

        return $userId == $this->user1_id ? $this->user2 : $this->user1;
    }

    /**
     * Get unread messages count for a specific user
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Scope to get conversations for a user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user1_id', $userId)
              ->orWhere('user2_id', $userId);
        });
    }

    /**
     * Scope to get conversations with another user
     */
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('user1_id', $user1Id)->where('user2_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('user1_id', $user2Id)->where('user2_id', $user1Id);
        });
    }

    /**
     * Check if a user is part of this conversation
     */
    public function hasUser($userId): bool
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }

    /**
     * Get the participant that is not the given user
     */
    public function getOtherParticipant($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user2;
        } elseif ($this->user2_id == $userId) {
            return $this->user1;
        }

        return null;
    }
}
