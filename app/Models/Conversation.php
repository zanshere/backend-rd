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

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function otherUser($userId = null)
    {
        if (!$userId) {
            return null;
        }

        return $userId == $this->user1_id ? $this->user2 : $this->user1;
    }

    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user1_id', $userId)
                ->orWhere('user2_id', $userId);
        });
    }

    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('user1_id', $user1Id)->where('user2_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('user1_id', $user2Id)->where('user2_id', $user1Id);
        });
    }

    public function hasUser($userId): bool
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }

    public function getOtherParticipant($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user2;
        } elseif ($this->user2_id == $userId) {
            return $this->user1;
        }

        return null;
    }

    // New method for online status
    public function getParticipantsOnlineStatus()
    {
        $user1Online = $this->user1->isOnline();
        $user2Online = $this->user2->isOnline();

        return [
            $this->user1_id => $user1Online,
            $this->user2_id => $user2Online,
        ];
    }

    /**
     * Get conversation data for serialization
     *
     * @return array
     */
    public function getSerializableDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'user1_id' => $this->user1_id,
            'user2_id' => $this->user2_id,
            'order_id' => $this->order_id,
            'title' => $this->title,
            'last_message_at' => $this->last_message_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user1' => $this->user1 ? $this->user1->serializable_data : null,
            'user2' => $this->user2 ? $this->user2->serializable_data : null,
            'order' => $this->order ? [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'user_id' => $this->order->user_id,
                'package_id' => $this->order->package_id,
                'status' => $this->order->status,
                'total_price' => $this->order->total_price,
                'created_at' => $this->order->created_at,
                'updated_at' => $this->order->updated_at,
            ] : null,
        ];
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user1_id' => $this->user1_id,
            'user2_id' => $this->user2_id,
            'order_id' => $this->order_id,
            'title' => $this->title,
            'last_message_at' => $this->last_message_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user1' => $this->user1 ? $this->user1->toArray() : null,
            'user2' => $this->user2 ? $this->user2->toArray() : null,
            'order' => $this->order ? [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'user_id' => $this->order->user_id,
                'package_id' => $this->order->package_id,
                'status' => $this->order->status,
                'total_price' => $this->order->total_price,
            ] : null,
        ];
    }
}
