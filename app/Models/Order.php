<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Order status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_IN_PROGRESS = 'progress';
    const STATUS_REVISION = 'revision';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    /**
     * Payment status constants
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'package_id',
        'custom_package_name',
        'custom_features',
        'description',
        'total_price',
        'paid_amount',
        'status',
        'payment_status',
        'progress_percentage',
        'deadline',
        'completed_at',
        'admin_notes',
        'customer_notes',
        'special_requirements',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_price' => 'decimal:0',
        'paid_amount' => 'decimal:0',
        'progress_percentage' => 'integer',
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'custom_features' => 'array',
        'special_requirements' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot function for generating order number
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');

        do {
            $number = $prefix . $date . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Get display total price
     */
    public function getDisplayTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Get display paid amount
     */
    public function getDisplayPaidAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->paid_amount, 0, ',', '.');
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if order is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_IN_PROGRESS, self::STATUS_REVISION]);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && !$this->isCompleted();
    }

    /**
     * Get remaining payment amount
     */
    public function getRemainingPaymentAttribute(): float
    {
        return max(0, $this->total_price - $this->paid_amount);
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for in-progress orders
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [self::STATUS_ACCEPTED, self::STATUS_IN_PROGRESS, self::STATUS_REVISION]);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for overdue orders
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Relationship with progress updates
     */
    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class)->latest();
    }

    /**
     * Relationship with order files
     */
    public function files()
    {
        return $this->hasMany(OrderFile::class);
    }

    /**
     * Relationship with feedback
     */
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    /**
     * Get latest progress update
     */
    public function latestProgress()
    {
        return $this->hasOne(ProgressUpdate::class)->latest();
    }

    /**
     * Check if order has feedback
     */
    public function hasFeedback(): bool
    {
        return $this->feedback !== null;
    }

    /**
     * Update order progress
     */
    public function updateProgress(int $percentage, ?string $notes = null): ProgressUpdate
    {
        $this->progress_percentage = $percentage;

        if ($percentage === 100) {
            $this->status = self::STATUS_COMPLETED;
            $this->completed_at = now();
        }

        $this->save();

        // Get the authenticated user ID safely
        $userId = auth()->id();

        // Create progress update record
        return $this->progressUpdates()->create([
            'progress_percentage' => $percentage,
            'notes' => $notes,
            'updated_by' => $userId,
        ]);
    }
}
