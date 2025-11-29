<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
            return true;
        }
        return false;
    }

    public function scopeUnreadForUser($query, $userId)
    {
        return $query->where('sender_id', '!=', $userId)
                    ->whereNull('read_at');
    }
}
