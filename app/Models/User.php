<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Role constants
     */
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'role',
        'status',
        'last_login_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('')
            ->upper();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Activate user account
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Deactivate user account
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Suspend user account
     */
    public function suspend(): bool
    {
        return $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): bool
    {
        return $this->update(['last_login_at' => now()]);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope for regular users
     */
    public function scopeRegular($query)
    {
        return $query->where('role', self::ROLE_USER);
    }

    /**
     * Scope for suspended users
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    /**
     * Scope for users with verified email
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Relationship with orders (user can have many orders)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relationship with feedbacks (user can have many feedbacks)
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Relationship with progress updates (admin can update progress)
     */
    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'updated_by');
    }

    /**
     * Relationship with uploaded files (admin can upload files)
     */
    public function uploadedFiles()
    {
        return $this->hasMany(OrderFile::class, 'uploaded_by');
    }

    /**
     * Relationship with feedback replies (admin can reply to feedback)
     */
    public function feedbackReplies()
    {
        return $this->hasMany(Feedback::class, 'replied_by');
    }

    /**
     * Get user's pending orders
     */
    public function pendingOrders()
    {
        return $this->orders()->where('status', Order::STATUS_PENDING);
    }

    /**
     * Get user's in-progress orders
     */
    public function inProgressOrders()
    {
        return $this->orders()->whereIn('status', [
            Order::STATUS_ACCEPTED,
            Order::STATUS_IN_PROGRESS,
            Order::STATUS_REVISION
        ]);
    }

    /**
     * Get user's completed orders
     */
    public function completedOrders()
    {
        return $this->orders()->where('status', Order::STATUS_COMPLETED);
    }

    /**
     * Get user's cancelled orders
     */
    public function cancelledOrders()
    {
        return $this->orders()->where('status', Order::STATUS_CANCELLED);
    }

    /**
     * Get user's total spending
     */
    public function getTotalSpendingAttribute(): float
    {
        return (float) $this->orders()
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('total_price');
    }

    /**
     * Get user's display total spending
     */
    public function getDisplayTotalSpendingAttribute(): string
    {
        return 'Rp ' . number_format($this->total_spending, 0, ',', '.');
    }

    /**
     * Get user's average rating from feedbacks
     */
    public function getAverageRatingAttribute(): ?float
    {
        $average = $this->feedbacks()->avg('rating');
        return $average ? round((float) $average, 1) : null;
    }

    /**
     * Get user's orders count by status
     */
    public function getOrdersCountByStatusAttribute(): array
    {
        return [
            'total' => $this->orders()->count(),
            'pending' => $this->pendingOrders()->count(),
            'in_progress' => $this->inProgressOrders()->count(),
            'completed' => $this->completedOrders()->count(),
            'cancelled' => $this->cancelledOrders()->count(),
        ];
    }

    /**
     * Check if user can create order
     */
    public function canCreateOrder(): bool
    {
        return $this->isActive() && $this->isUser();
    }

    /**
     * Check if user can manage orders (admin only)
     */
    public function canManageOrders(): bool
    {
        return $this->isAdmin() && $this->isActive();
    }

    /**
     * Check if user can manage users (admin only)
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin() && $this->isActive();
    }

    /**
     * Get user's role display name
     */
    public function getRoleDisplayNameAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_USER => 'User',
            default => 'Unknown',
        };
    }

    /**
     * Get user's status display name
     */
    public function getStatusDisplayNameAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Tidak Aktif',
            self::STATUS_SUSPENDED => 'Ditangguhkan',
            default => 'Unknown',
        };
    }

    /**
     * Get user's status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'green',
            self::STATUS_INACTIVE => 'gray',
            self::STATUS_SUSPENDED => 'red',
            default => 'gray',
        };
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Set default role and status when creating new user
        static::creating(function ($user) {
            if (empty($user->role)) {
                $user->role = self::ROLE_USER;
            }
            if (empty($user->status)) {
                $user->status = self::STATUS_ACTIVE;
            }
        });
    }
}
