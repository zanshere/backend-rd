<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    /**
     * Rating constants
     */
    const RATING_EXCELLENT = 5;
    const RATING_GOOD = 4;
    const RATING_AVERAGE = 3;
    const RATING_POOR = 2;
    const RATING_VERY_POOR = 1;

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_READ = 'read';
    const STATUS_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'rating',
        'comment',
        'suggestions',
        'status',
        'admin_reply',
        'replied_by',
        'replied_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship with user who gave feedback
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with admin who replied
     */
    public function replier()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Get rating as stars
     */
    public function getRatingStarsAttribute(): string
    {
        return str_repeat('⭐', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get rating label
     */
    public function getRatingLabelAttribute(): string
    {
        $labels = [
            self::RATING_VERY_POOR => 'Sangat Buruk',
            self::RATING_POOR => 'Buruk',
            self::RATING_AVERAGE => 'Cukup',
            self::RATING_GOOD => 'Baik',
            self::RATING_EXCELLENT => 'Sangat Baik',
        ];

        return $labels[$this->rating] ?? 'Tidak Ada Rating';
    }

    /**
     * Check if feedback has admin reply
     */
    public function hasReply(): bool
    {
        return !empty($this->admin_reply);
    }

    /**
     * Check if feedback is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Mark feedback as read
     */
    public function markAsRead(): bool
    {
        return $this->update(['status' => self::STATUS_READ]);
    }

    /**
     * Add admin reply
     */
    public function addReply(string $reply, int $adminId): bool
    {
        return $this->update([
            'admin_reply' => $reply,
            'replied_by' => $adminId,
            'replied_at' => now(),
            'status' => self::STATUS_READ,
        ]);
    }

    /**
     * Scope for pending feedbacks
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for read feedbacks
     */
    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    /**
     * Scope for feedbacks with replies
     */
    public function scopeWithReplies($query)
    {
        return $query->whereNotNull('admin_reply');
    }

    /**
     * Scope for high rating feedbacks (4-5 stars)
     */
    public function scopeHighRating($query)
    {
        return $query->whereIn('rating', [self::RATING_GOOD, self::RATING_EXCELLENT]);
    }

    /**
     * Scope for low rating feedbacks (1-2 stars)
     */
    public function scopeLowRating($query)
    {
        return $query->whereIn('rating', [self::RATING_VERY_POOR, self::RATING_POOR]);
    }
}
