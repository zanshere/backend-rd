<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Payment method constants
     */
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_EWALLET = 'ewallet';
    const METHOD_QRIS = 'qris';

    /**
     * Payment status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REFUNDED = 'refunded';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'payment_number',
        'amount',
        'method',
        'status',
        'payment_proof',
        'paid_at',
        'due_date',
        'notes',
        'reference_number',
        'bank_name',
        'account_number',
        'account_holder',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:0',
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot function for generating payment number
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = static::generatePaymentNumber();
            }
        });
    }

    /**
     * Generate unique payment number
     */
    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');

        do {
            $number = $prefix . $date . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('payment_number', $number)->exists());

        return $number;
    }

    /**
     * Relationship with order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get display amount
     */
    public function getDisplayAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->isPending();
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(?string $referenceNumber = null): bool
    {
        return $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'reference_number' => $referenceNumber,
        ]);
    }

    /**
     * Get payment method label
     */
    public function getMethodLabelAttribute(): string
    {
        $labels = [
            self::METHOD_BANK_TRANSFER => 'Transfer Bank',
            self::METHOD_CREDIT_CARD => 'Kartu Kredit',
            self::METHOD_EWALLET => 'E-Wallet',
            self::METHOD_QRIS => 'QRIS',
        ];

        return $labels[$this->method] ?? 'Unknown';
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for overdue payments
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
}
