<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedbacks';

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
     * Type constants
     */
    const TYPE_SUGGESTION = 'suggestion';
    const TYPE_COMPLAINT = 'complaint';
    const TYPE_BUG = 'bug';
    const TYPE_FEATURE = 'feature';
    const TYPE_OTHER = 'other';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'rating',
        'message',
        'status',
        'response',
        'responded_by',
        'responded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'responded_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
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
     * Scope for archived feedbacks
     */
    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope with rating
     */
    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }

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
     * Relationship with admin who responded
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Check if feedback has response
     */
    public function getHasResponseAttribute()
    {
        return !is_null($this->response);
    }

    /**
     * Get rating text
     */
    public function getRatingTextAttribute()
    {
        return match($this->rating) {
            self::RATING_EXCELLENT => 'Sangat Baik',
            self::RATING_GOOD => 'Baik',
            self::RATING_AVERAGE => 'Cukup',
            self::RATING_POOR => 'Buruk',
            self::RATING_VERY_POOR => 'Sangat Buruk',
            default => 'Tidak ada rating',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_SUGGESTION => 'Saran',
            self::TYPE_COMPLAINT => 'Keluhan',
            self::TYPE_BUG => 'Bug/Error',
            self::TYPE_FEATURE => 'Permintaan Fitur',
            self::TYPE_OTHER => 'Lainnya',
            default => $this->type,
        };
    }

    /**
     * Check if feedback is read
     */
    public function getIsReadAttribute()
    {
        return $this->status === self::STATUS_READ || !is_null($this->response);
    }

    /**
     * Get user initials for avatar
     */
    public function getUserInitialsAttribute()
    {
        $name = $this->user->name ?? 'U';
        $initials = '';
        $words = explode(' ', $name);

        foreach ($words as $word) {
            if (strlen($initials) >= 2) break;
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials ?: 'U';
    }

    /**
     * Scope for unread feedbacks
     */
    public function scopeUnread($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for responded feedbacks
     */
    public function scopeResponded($query)
    {
        return $query->whereNotNull('response');
    }

    /**
     * Get badge color based on type
     */
    public function getBadgeColorAttribute()
    {
        return match($this->type) {
            self::TYPE_SUGGESTION => 'green',
            self::TYPE_COMPLAINT => 'red',
            self::TYPE_BUG => 'orange',
            self::TYPE_FEATURE => 'blue',
            self::TYPE_OTHER => 'gray',
            default => 'gray',
        };
    }
}
