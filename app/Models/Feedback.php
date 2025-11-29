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
    protected $table = 'feedbacks'; // Tambahkan ini

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
}
