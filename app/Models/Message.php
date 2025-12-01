<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Notifications\NewMessageNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['is_read', 'delivery_status', 'formatted_time'];

    protected static function booted()
    {
        static::created(function ($message) {
            // Update conversation last message time
            $message->conversation->update([
                'last_message_at' => $message->created_at,
            ]);

            // Broadcast event untuk real-time message
            broadcast(new MessageSent($message))->toOthers();

            // Kirim notifikasi ke recipient
            $conversation = $message->conversation;
            $recipientId = $conversation->user1_id == $message->sender_id
                ? $conversation->user2_id
                : $conversation->user1_id;

            $recipient = User::find($recipientId);

            if ($recipient && $recipientId != $message->sender_id) {
                // Kirim database notification dan broadcast notification
                $recipient->notify(new NewMessageNotification($message));

                // Also send custom broadcast notification
                broadcast(new \App\Events\MessageNotification(
                    $recipientId,
                    $message
                ))->toOthers();
            }
        });

        static::updated(function ($message) {
            // Broadcast ketika message di-update (misalnya read_at berubah)
            if ($message->wasChanged('read_at') && $message->read_at) {
                broadcast(new MessageRead(
                    $message->conversation_id,
                    $message->sender_id,
                    [$message->id]
                ))->toOthers();
            }
        });
    }

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

    public function getDeliveryStatusAttribute(): string
    {
        if (!is_null($this->read_at)) {
            return 'read';
        }

        // Check if message was sent (created more than 1 second ago)
        if ($this->created_at && $this->created_at->diffInSeconds(now()) > 1) {
            return 'delivered';
        }

        return 'sent';
    }

    public function getFormattedTimeAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }

        if ($this->created_at->isToday()) {
            return $this->created_at->format('H:i');
        } elseif ($this->created_at->isYesterday()) {
            return 'Kemarin ' . $this->created_at->format('H:i');
        } else {
            return $this->created_at->format('d/m/Y H:i');
        }
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

    public function getReadStatusAttribute(): string
    {
        return $this->is_read ? 'Sudah dibaca' : 'Belum dibaca';
    }

    public function getTimeAgoAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }

        return $this->created_at->diffForHumans();
    }

    public function hasAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    public function attachmentsCount(): int
    {
        return $this->attachments()->count();
    }

    public function getFirstAttachmentAttribute()
    {
        return $this->attachments()->first();
    }
}
