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
const STATUS_DRAFT = 'draft';
const STATUS_PENDING = 'pending';
const STATUS_CONFIRMED = 'confirmed';      // Untuk "accepted"
const STATUS_IN_PROGRESS = 'progress';     // Perhatikan: 'progress' bukan 'in_progress'
const STATUS_COMPLETED = 'completed';
const STATUS_CANCELLED = 'cancelled';      // Untuk "rejected"

    /**
     * Payment status constants
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_EXPIRED = 'expired';

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
        'project_name',
        'domain_name',
        'special_requirements',
        'base_price',
        'discount_amount',
        'total_price',
        'status',
        'payment_status',
        'payment_url',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'midtrans_merchant_id',
        'paid_amount',
        'admin_notes',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_price' => 'decimal:0',
        'discount_amount' => 'decimal:0',
        'total_price' => 'decimal:0',
        'paid_amount' => 'decimal:0',
        'special_requirements' => 'array',
        'paid_at' => 'datetime',
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
     * Get the overall progress percentage
     */
    public function getOverallProgressAttribute(): int
    {
        // Jika ada progress updates dari tabel progress_updates
        if ($this->progressUpdates()->exists()) {
            $latestProgress = $this->progressUpdates()->latest()->first();
            return $latestProgress->progress_percentage;
        }

        // Default progress berdasarkan status
        return match($this->status) {
            self::STATUS_DRAFT => 0,
            self::STATUS_PENDING => 10,
            self::STATUS_CONFIRMED => 30,
            self::STATUS_IN_PROGRESS => 60,
            self::STATUS_COMPLETED => 100,
            self::STATUS_CANCELLED => 0,
            default => 0,
        };
    }

    /**
     * Get display base price
     */
    public function getDisplayBasePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    /**
     * Get display discount amount
     */
    public function getDisplayDiscountAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
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
        return $this->paid_amount ? 'Rp ' . number_format($this->paid_amount, 0, ',', '.') : '-';
    }

    /**
     * Check if order is draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if order is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if order is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
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
     * Check if order payment is failed
     */
    public function isPaymentFailed(): bool
    {
        return $this->payment_status === self::PAYMENT_FAILED;
    }

    /**
     * Check if order payment is expired
     */
    public function isPaymentExpired(): bool
    {
        return $this->payment_status === self::PAYMENT_EXPIRED;
    }

    /**
     * Check if order has special requirements
     */
    public function hasSpecialRequirements(): bool
    {
        return !empty($this->special_requirements) && is_array($this->special_requirements) && count($this->special_requirements) > 0;
    }

    /**
     * Scope for draft orders
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for confirmed orders
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for in-progress orders
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
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
     * Scope for active orders (not completed or cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
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
     * Mark order as confirmed
     */
    public function markAsConfirmed(): bool
    {
        $this->status = self::STATUS_CONFIRMED;
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
     * Mark order as completed
     */
    public function markAsCompleted(): bool
    {
        $this->status = self::STATUS_COMPLETED;
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
     * Update payment status
     */
    public function updatePaymentStatus(string $status, ?float $amount = null): bool
    {
        $this->payment_status = $status;

        if ($status === self::PAYMENT_PAID) {
            $this->paid_at = now();
            $this->paid_amount = $amount ?? $this->total_price;
            if ($this->status === self::STATUS_PENDING) {
                $this->status = self::STATUS_CONFIRMED;
            }
        }

        return $this->save();
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_IN_PROGRESS => 'indigo',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
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
            self::PAYMENT_FAILED => 'red',
            self::PAYMENT_EXPIRED => 'orange',
            default => 'gray',
        };
    }

/**
 * Status display names
 */
public static function getStatusDisplayNames(): array
{
    return [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Menunggu Konfirmasi',
        self::STATUS_CONFIRMED => 'Dikonfirmasi',
        self::STATUS_IN_PROGRESS => 'Dalam Pengerjaan',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];
}

    /**
     * Get payment status display name
     */
    public function getPaymentStatusDisplayNameAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_PENDING => 'Menunggu Pembayaran',
            self::PAYMENT_PAID => 'Lunas',
            self::PAYMENT_FAILED => 'Pembayaran Gagal',
            self::PAYMENT_EXPIRED => 'Pembayaran Kadaluarsa',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get package name
     */
    public function getPackageNameAttribute(): string
    {
        return $this->package->name ?? 'Paket Tidak Diketahui';
    }

    /**
     * Get package type
     */
    public function getPackageTypeAttribute(): string
    {
        return $this->package->type ?? 'unknown';
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
     * Get formatted paid at
     */
    public function getFormattedPaidAtAttribute(): ?string
    {
        return $this->paid_at?->format('d M Y H:i');
    }

    /**
     * Get special requirements as text
     */
    public function getSpecialRequirementsTextAttribute(): ?string
    {
        if (!$this->hasSpecialRequirements()) {
            return null;
        }

        return $this->special_requirements['kebutuhan_khusus'] ?? implode(', ', $this->special_requirements);
    }

    /**
     * Get order timeline events
     */
    public function getTimelineEventsAttribute(): array
    {
        $events = [];

        // Order created
        $events[] = [
            'title' => 'Pesanan Dibuat',
            'description' => 'Pesanan berhasil dibuat',
            'date' => $this->created_at,
            'icon' => 'shopping-cart',
            'color' => 'blue',
        ];

        // Payment events
        if ($this->paid_at) {
            $events[] = [
                'title' => 'Pembayaran Berhasil',
                'description' => 'Pembayaran telah diterima',
                'date' => $this->paid_at,
                'icon' => 'credit-card',
                'color' => 'green',
            ];
        }

        // Status changes
        if ($this->status !== self::STATUS_DRAFT && $this->status !== self::STATUS_PENDING) {
            $events[] = [
                'title' => 'Pesanan Diproses',
                'description' => 'Pesanan sedang diproses oleh tim',
                'date' => $this->updated_at,
                'icon' => 'settings',
                'color' => 'indigo',
            ];
        }

        // Progress updates
        foreach ($this->progressUpdates()->orderBy('created_at', 'desc')->get() as $progress) {
            $events[] = [
                'title' => 'Update Progress: ' . $progress->progress_label,
                'description' => $progress->notes ?? 'Progress diperbarui',
                'date' => $progress->created_at,
                'icon' => 'activity',
                'color' => 'purple',
            ];
        }

        // Sort by date
        usort($events, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return $events;
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING])
            && $this->payment_status !== self::PAYMENT_PAID;
    }

    /**
     * Check if order requires payment
     */
    public function requiresPayment(): bool
    {
        return $this->payment_status === self::PAYMENT_PENDING
            && !$this->package->is_custom_price;
    }


}
