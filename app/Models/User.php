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
        'notification_settings',
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
            'notification_settings' => 'array',
        ];
    }

    /**
     * Default values for attributes
     */
    protected $attributes = [
        'notification_settings' => '{
            "email": {
                "order_updates": true,
                "messages": true,
                "system": true
            },
            "push": {
                "order_updates": true,
                "messages": true,
                "promotions": false
            }
        }',
        'role' => self::ROLE_USER,
        'status' => self::STATUS_ACTIVE,
    ];

    /**
     * Get the user's initials - FIXED VERSION
     */
    public function initials(): string
    {
        $name = $this->name;

        // Handle empty name
        if (empty($name)) {
            return '??';
        }

        $words = explode(' ', $name);
        $initials = '';

        // Take first 2 words maximum
        $maxWords = min(count($words), 2);
        for ($i = 0; $i < $maxWords; $i++) {
            if (!empty($words[$i])) {
                $initials .= strtoupper(substr($words[$i], 0, 1));
            }
        }

        return $initials;
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
        return $this->hasMany(Feedback::class, 'responded_by');
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
     * Relationship with sent messages
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Relationship with conversations as user1
     */
    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    /**
     * Relationship with conversations as user2
     */
    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    /**
     * Get all conversations for this user
     */
    public function conversations()
    {
        return Conversation::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id)
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * Check if user can receive notifications
     */
    public function canReceiveNotifications(): bool
    {
        return $this->isActive();
    }

    /**
     * Check if user can send messages
     */
    public function canSendMessages(): bool
    {
        return $this->isActive();
    }

    /**
     * Get user's unread messages count
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return Message::whereHas('conversation', function ($query) {
            $query->where('user1_id', $this->id)
                  ->orWhere('user2_id', $this->id);
        })
        ->where('sender_id', '!=', $this->id)
        ->whereNull('read_at')
        ->count();
    }

    /**
     * Get user's recent conversations with unread counts
     */
    public function getRecentConversationsAttribute()
    {
        return $this->conversations()
            ->with(['user1', 'user2', 'lastMessage'])
            ->take(5)
            ->get()
            ->map(function ($conversation) {
                $conversation->unread_count = $conversation->messages()
                    ->where('sender_id', '!=', $this->id)
                    ->whereNull('read_at')
                    ->count();
                $conversation->other_user = $conversation->user1_id == $this->id
                    ? $conversation->user2
                    : $conversation->user1;
                return $conversation;
            });
    }

    /**
     * Get user's notification settings with defaults
     */
    public function getNotificationSettingsAttribute($value): array
    {
        $defaultSettings = [
            'email' => [
                'order_updates' => true,
                'messages' => true,
                'system' => true,
            ],
            'push' => [
                'order_updates' => true,
                'messages' => true,
                'promotions' => false,
            ],
        ];

        if (is_array($value)) {
            return array_merge($defaultSettings, $value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? array_merge($defaultSettings, $decoded) : $defaultSettings;
        }

        return $defaultSettings;
    }

    /**
     * Set user's notification settings
     */
    public function setNotificationSettingsAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['notification_settings'] = json_encode($value);
        } else {
            $this->attributes['notification_settings'] = $value;
        }
    }

    /**
     * Check if user has email notification enabled for specific type
     */
    public function hasEmailNotification(string $type): bool
    {
        return $this->notification_settings['email'][$type] ?? true;
    }

    /**
     * Check if user has push notification enabled for specific type
     */
    public function hasPushNotification(string $type): bool
    {
        return $this->notification_settings['push'][$type] ?? true;
    }

    /**
     * Update user's notification settings
     */
    public function updateNotificationSettings(array $settings): bool
    {
        $currentSettings = $this->notification_settings;
        $mergedSettings = array_merge($currentSettings, $settings);

        return $this->update(['notification_settings' => $mergedSettings]);
    }

    /**
     * Get user's dashboard statistics
     */
    public function getDashboardStatsAttribute(): array
    {
        $stats = [
            'total_orders' => $this->orders()->count(),
            'pending_orders' => $this->pendingOrders()->count(),
            'in_progress_orders' => $this->inProgressOrders()->count(),
            'completed_orders' => $this->completedOrders()->count(),
            'total_spending' => $this->total_spending,
            'average_rating' => $this->average_rating,
            'unread_messages' => $this->unread_messages_count,
        ];

        if ($this->isAdmin()) {
            $stats['total_users'] = User::regular()->count();
            $stats['total_feedbacks'] = Feedback::count();
            $stats['pending_feedbacks'] = Feedback::where('status', Feedback::STATUS_PENDING)->count();
        }

        return $stats;
    }

    /**
     * Get user's activity timeline
     */
    public function getActivityTimelineAttribute()
    {
        $activities = collect();

        // Recent orders
        $recentOrders = $this->orders()
            ->with('package')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'title' => 'Pesanan Baru: ' . ($order->package->name ?? 'Unknown Package'),
                    'description' => 'Order #' . $order->order_number,
                    'timestamp' => $order->created_at,
                    'status' => $order->status,
                ];
            });

        // Recent feedbacks
        $recentFeedbacks = $this->feedbacks()
            ->with('order')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($feedback) {
                return [
                    'type' => 'feedback',
                    'title' => 'Feedback ' . ucfirst($feedback->type),
                    'description' => Str::limit($feedback->message, 50),
                    'timestamp' => $feedback->created_at,
                    'status' => $feedback->status,
                ];
            });

        // Recent messages
        $recentMessages = $this->sentMessages()
            ->with('conversation')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($message) {
                return [
                    'type' => 'message',
                    'title' => 'Pesan Terkirim',
                    'description' => Str::limit($message->content, 50),
                    'timestamp' => $message->created_at,
                    'status' => 'sent',
                ];
            });

        $activities = $activities
            ->merge($recentOrders)
            ->merge($recentFeedbacks)
            ->merge($recentMessages)
            ->sortByDesc('timestamp')
            ->take(5);

        return $activities;
    }

    /**
     * Get user's preferred communication method
     */
    public function getPreferredCommunicationMethodAttribute(): string
    {
        $settings = $this->notification_settings;

        if ($settings['email']['messages'] ?? true) {
            return 'email';
        } elseif ($settings['push']['messages'] ?? true) {
            return 'push';
        } else {
            return 'in_app';
        }
    }

    /**
     * Check if user has unread notifications
     */
    public function hasUnreadNotifications(): bool
    {
        return $this->unreadNotifications()->exists();
    }

    /**
     * Get user's unread notifications count
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(): void
    {
        $this->unreadNotifications->markAsRead();
    }

    /**
     * Get user's profile completion percentage
     */
    public function getProfileCompletionPercentageAttribute(): int
    {
        $fields = [
            'name' => !empty($this->name),
            'email' => !empty($this->email) && $this->hasVerifiedEmail(),
            'phone' => !empty($this->phone),
            'company_name' => !empty($this->company_name),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Check if user has verified email
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get user's display name with role badge
     */
    public function getDisplayNameWithRoleAttribute(): string
    {
        $roleBadge = match($this->role) {
            self::ROLE_ADMIN => '<span class="badge badge-admin">Admin</span>',
            self::ROLE_USER => '<span class="badge badge-user">User</span>',
            default => '<span class="badge badge-unknown">Unknown</span>',
        };

        return $this->name . ' ' . $roleBadge;
    }

    /**
     * Get user's safe data for public display
     */
    public function getPublicDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'initials' => $this->initials(),
            'company_name' => $this->company_name,
            'role' => $this->role,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'is_online' => $this->last_login_at && $this->last_login_at->gt(now()->subMinutes(5)),
        ];
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
            if (empty($user->notification_settings)) {
                $user->notification_settings = [
                    'email' => [
                        'order_updates' => true,
                        'messages' => true,
                        'system' => true,
                    ],
                    'push' => [
                        'order_updates' => true,
                        'messages' => true,
                        'promotions' => false,
                    ],
                ];
            }
        });

        // Generate avatar when user is created
        static::created(function ($user) {
            // You can add avatar generation logic here if needed
        });
    }
}
