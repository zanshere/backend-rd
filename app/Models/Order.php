<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

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
     * Get remaining payment amount
     */
    public function getRemainingPaymentAttribute(): float
    {
        return max(0, $this->total_price - $this->paid_amount);
    }

    /**
     * Get display remaining payment
     */
    public function getDisplayRemainingPaymentAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_payment, 0, ',', '.');
    }

    /**
     * Get progress percentage with default value
     */
    public function getProgressPercentageAttribute($value): int
    {
        return $value ?? 0;
    }

    /**
     * Get paid amount with default value
     */
    public function getPaidAmountAttribute($value): float
    {
        return $value ?? 0;
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if order is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if order is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if order is in revision
     */
    public function isRevision(): bool
    {
        return $this->status === self::STATUS_REVISION;
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if order is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Check if order payment is pending
     */
    public function isPaymentPending(): bool
    {
        return $this->payment_status === self::PAYMENT_PENDING;
    }

    /**
     * Check if order payment is partial
     */
    public function isPaymentPartial(): bool
    {
        return $this->payment_status === self::PAYMENT_PARTIAL;
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && !$this->isCompleted() && !$this->isCancelled();
    }

    /**
     * Check if order has custom package
     */
    public function hasCustomPackage(): bool
    {
        return !empty($this->custom_package_name);
    }

    /**
     * Check if order has custom features
     */
    public function hasCustomFeatures(): bool
    {
        return !empty($this->custom_features) && is_array($this->custom_features) && count($this->custom_features) > 0;
    }

    /**
     * Check if order has special requirements
     */
    public function hasSpecialRequirements(): bool
    {
        return !empty($this->special_requirements) && is_array($this->special_requirements) && count($this->special_requirements) > 0;
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for accepted orders
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope for in-progress orders
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for revision orders
     */
    public function scopeRevision($query)
    {
        return $query->where('status', self::STATUS_REVISION);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope for rejected orders
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for active orders (not completed or cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_REJECTED]);
    }

    /**
     * Scope for overdue orders
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_REJECTED]);
    }

    /**
     * Scope for orders with pending payment
     */
    public function scopePaymentPending($query)
    {
        return $query->where('payment_status', self::PAYMENT_PENDING);
    }

    /**
     * Scope for orders with paid payment
     */
    public function scopePaymentPaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    /**
     * Scope for orders with partial payment
     */
    public function scopePaymentPartial($query)
    {
        return $query->where('payment_status', self::PAYMENT_PARTIAL);
    }

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Relationship with progress updates
     */
    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class, 'order_id');
    }

    /**
     * Relationship with order files
     */
    public function files(): HasMany
    {
        return $this->hasMany(OrderFile::class, 'order_id');
    }

    /**
     * Relationship with feedback
     */
    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'order_id');
    }

    /**
     * Get latest progress update
     */
    public function latestProgress(): HasOne
    {
        return $this->hasOne(ProgressUpdate::class, 'order_id')->latestOfMany();
    }

    /**
     * Check if order has feedback
     */
    public function hasFeedback(): bool
    {
        return $this->feedback()->exists();
    }

    /**
     * Check if order has progress updates
     */
    public function hasProgressUpdates(): bool
    {
        return $this->progressUpdates()->exists();
    }

    /**
     * Check if order has files
     */
    public function hasFiles(): bool
    {
        return $this->files()->exists();
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
        $userId = auth()->id() ?? 1; // Fallback to admin user if not authenticated

        // Create progress update record
        return $this->progressUpdates()->create([
            'progress_percentage' => $percentage,
            'notes' => $notes,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        $this->progress_percentage = 100;
        $this->completed_at = now();

        return $this->save();
    }

    /**
     * Mark order as cancelled
     */
    public function markAsCancelled(): bool
    {
        $this->status = self::STATUS_CANCELLED;

        return $this->save();
    }

    /**
     * Mark order as in progress
     */
    public function markAsInProgress(): bool
    {
        $this->status = self::STATUS_IN_PROGRESS;

        return $this->save();
    }

    /**
     * Mark order as revision
     */
    public function markAsRevision(): bool
    {
        $this->status = self::STATUS_REVISION;

        return $this->save();
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(string $status, ?float $paidAmount = null): bool
    {
        $this->payment_status = $status;

        if ($paidAmount !== null) {
            $this->paid_amount = $paidAmount;
        }

        return $this->save();
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_ACCEPTED => 'blue',
            self::STATUS_IN_PROGRESS => 'indigo',
            self::STATUS_REVISION => 'orange',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_REJECTED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeColorAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_PENDING => 'yellow',
            self::PAYMENT_PAID => 'green',
            self::PAYMENT_PARTIAL => 'blue',
            self::PAYMENT_FAILED => 'red',
            self::PAYMENT_REFUNDED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayNameAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_ACCEPTED => 'Diterima',
            self::STATUS_IN_PROGRESS => 'Dalam Pengerjaan',
            self::STATUS_REVISION => 'Revisi',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get payment status display name
     */
    public function getPaymentStatusDisplayNameAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_PENDING => 'Menunggu Pembayaran',
            self::PAYMENT_PAID => 'Lunas',
            self::PAYMENT_PARTIAL => 'Pembayaran Sebagian',
            self::PAYMENT_FAILED => 'Pembayaran Gagal',
            self::PAYMENT_REFUNDED => 'Dikembalikan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get days remaining until deadline
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }

        $now = now();
        $deadline = $this->deadline;

        if ($deadline->isPast()) {
            return 0;
        }

        return $now->diffInDays($deadline, false);
    }

    /**
     * Get package name (fallback to custom package name)
     */
    public function getPackageNameAttribute(): string
    {
        if ($this->package) {
            return $this->package->name;
        }

        return $this->custom_package_name ?? 'Paket Kustom';
    }

    /**
     * Get customer name
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->user->name ?? 'Tidak Diketahui';
    }

    /**
     * Get customer email
     */
    public function getCustomerEmailAttribute(): string
    {
        return $this->user->email ?? 'Tidak Diketahui';
    }

    /**
     * Get formatted created at
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d M Y H:i');
    }

    /**
     * Get formatted deadline
     */
    public function getFormattedDeadlineAttribute(): ?string
    {
        return $this->deadline?->format('d M Y');
    }

    /**
     * Get formatted completed at
     */
    public function getFormattedCompletedAtAttribute(): ?string
    {
        return $this->completed_at?->format('d M Y H:i');
    }
}
