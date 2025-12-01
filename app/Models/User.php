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
    const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_PENDING = 'pending';

    /**
     * Notification types constants
     */
    const NOTIFICATION_ORDER_UPDATES = 'order_updates';
    const NOTIFICATION_MESSAGES = 'messages';
    const NOTIFICATION_SYSTEM = 'system';
    const NOTIFICATION_PROMOTIONS = 'promotions';
    const NOTIFICATION_SECURITY = 'security';

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
        'company_address',
        'company_phone',
        'company_website',
        'avatar',
        'role',
        'status',
        'last_login_at',
        'email_verified_at',
        'phone_verified_at',
        'notification_settings',
        'last_activity_at',
        'is_online',
        'timezone',
        'language',
        'currency',
        'date_format',
        'time_format',
        'last_activity_at',
        'is_online',
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
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'notification_settings' => 'array',
            'is_online' => 'boolean',
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
                "system": true,
                "promotions": false,
                "security": true
            },
            "push": {
                "order_updates": true,
                "messages": true,
                "promotions": false,
                "system": true
            },
            "in_app": {
                "order_updates": true,
                "messages": true,
                "system": true,
                "promotions": false
            }
        }',
        'role' => self::ROLE_USER,
        'status' => self::STATUS_ACTIVE,
        'is_online' => false,
        'timezone' => 'Asia/Jakarta',
        'language' => 'id',
        'currency' => 'IDR',
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
    ];

    /**
     * Boot the model and its traits.
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
                        'promotions' => false,
                        'security' => true,
                    ],
                    'push' => [
                        'order_updates' => true,
                        'messages' => true,
                        'promotions' => false,
                        'system' => true,
                    ],
                    'in_app' => [
                        'order_updates' => true,
                        'messages' => true,
                        'system' => true,
                        'promotions' => false,
                    ],
                ];
            }
        });

        // Update last activity when user is retrieved (for online status)
        static::retrieved(function ($user) {
            if ($user->isOnline() && $user->last_activity_at && $user->last_activity_at->lt(now()->subMinutes(2))) {
                $user->updateLastActivity();
            }
        });
    }

    /**
     * Get the channels that user receives broadcast notifications on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'App.Models.User.' . $this->id;
    }

    /**
     * Get the presence channel for user's online status
     *
     * @return string
     */
    public function presenceChannel(): string
    {
        return 'user.' . $this->id . '.presence';
    }

    /**
     * Get the conversations channel for user
     *
     * @return string
     */
    public function conversationsChannel(): string
    {
        return 'user.' . $this->id . '.conversations';
    }

    /**
     * Get user's broadcast data for presence channels
     *
     * @return array
     */
    public function getBroadcastData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'initials' => $this->initials,
            'company_name' => $this->company_name,
            'is_online' => $this->isOnline(),
            'last_seen' => $this->last_activity_at?->diffForHumans() ?? $this->last_login_at?->diffForHumans(),
            'avatar_color' => $this->avatar_color,
            'status' => $this->status,
            'status_display' => $this->status_display,
            'role_display' => $this->role_display,
        ];
    }

    /**
     * Get user's simplified broadcast data for public display
     *
     * @return array
     */
    public function getPublicBroadcastData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'initials' => $this->initials,
            'company_name' => $this->company_name,
            'role' => $this->role,
            'is_online' => $this->isOnline(),
            'last_seen' => $this->last_activity_at?->diffForHumans() ?? $this->last_login_at?->diffForHumans(),
            'avatar_color' => $this->avatar_color,
        ];
    }

    /**
     * Get user's initials attribute
     *
     * @return string
     */
    public function getInitialsAttribute(): string
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
     * Get user's avatar color based on user ID
     *
     * @return string
     */
    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'bg-blue-500',
            'bg-green-500',
            'bg-purple-500',
            'bg-pink-500',
            'bg-indigo-500',
            'bg-teal-500',
            'bg-orange-500',
            'bg-cyan-500',
            'bg-rose-500',
            'bg-amber-500',
            'bg-lime-500',
            'bg-emerald-500'
        ];

        $index = $this->id % count($colors);
        return $colors[$index];
    }

    /**
     * Get user's avatar URL (if using gravatar or custom avatars)
     *
     * @return string
     */
    public function getAvatarUrlAttribute(): string
    {
        // If user has custom avatar
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        // Fallback to Gravatar
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=200";
    }

    /**
     * Check if user is currently online (based on last activity)
     *
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->last_activity_at &&
            $this->last_activity_at->gt(now()->subMinutes(5));
    }

    /**
     * Update user's last activity for online status
     *
     * @return bool
     */
    public function updateLastActivity(): bool
    {
        $this->last_activity_at = now();
        $this->is_online = true;
        return $this->save();
    }

    /**
     * Mark user as offline
     *
     * @return bool
     */
    public function markAsOffline(): bool
    {
        $this->is_online = false;
        return $this->save();
    }

    /**
     * Update user's last login timestamp and mark as online
     *
     * @return bool
     */
    public function updateLastLogin(): bool
    {
        $this->last_login_at = now();
        $this->last_activity_at = now();
        $this->is_online = true;
        return $this->save();
    }

    /**
     * Get user data for serialization (to avoid Serialization of 'Closure' error)
     *
     * @return array
     */
    public function getSerializableDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'initials' => $this->initials,
            'is_online' => $this->isOnline(),
            'last_seen' => $this->last_activity_at?->diffForHumans() ?? $this->last_login_at?->diffForHumans(),
            'avatar_color' => $this->avatar_color,
            'avatar_url' => $this->avatar_url,
            'last_login_at' => $this->last_login_at,
            'last_activity_at' => $this->last_activity_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'initials' => $this->initials,
            'is_online' => $this->isOnline(),
            'last_seen' => $this->last_activity_at?->diffForHumans() ?? $this->last_login_at?->diffForHumans(),
            'avatar_color' => $this->avatar_color,
            'avatar_url' => $this->avatar_url,
            'last_login_at' => $this->last_login_at,
            'last_activity_at' => $this->last_activity_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if user is super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if user is regular user
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if user is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is inactive
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Check if user is suspended
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Check if user is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Activate user account
     *
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Deactivate user account
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Suspend user account
     *
     * @return bool
     */
    public function suspend(): bool
    {
        return $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    /**
     * Mark user as pending
     *
     * @return bool
     */
    public function markAsPending(): bool
    {
        return $this->update(['status' => self::STATUS_PENDING]);
    }

    /**
     * Get user's role display name
     *
     * @return string
     */
    public function getRoleDisplayAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Administrator',
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_USER => 'User',
            default => 'Unknown',
        };
    }

    /**
     * Get user's status display name
     *
     * @return string
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Tidak Aktif',
            self::STATUS_SUSPENDED => 'Ditangguhkan',
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            default => 'Unknown',
        };
    }

    /**
     * Get user's status badge color
     *
     * @return string
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'green',
            self::STATUS_INACTIVE => 'gray',
            self::STATUS_SUSPENDED => 'red',
            self::STATUS_PENDING => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get user's role badge color
     *
     * @return string
     */
    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'purple',
            self::ROLE_ADMIN => 'blue',
            self::ROLE_USER => 'green',
            default => 'gray',
        };
    }

    /**
     * Get user's notification settings with defaults
     *
     * @param mixed $value
     * @return array
     */
    public function getNotificationSettingsAttribute($value): array
    {
        $defaultSettings = [
            'email' => [
                'order_updates' => true,
                'messages' => true,
                'system' => true,
                'promotions' => false,
                'security' => true,
            ],
            'push' => [
                'order_updates' => true,
                'messages' => true,
                'promotions' => false,
                'system' => true,
            ],
            'in_app' => [
                'order_updates' => true,
                'messages' => true,
                'system' => true,
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
     *
     * @param mixed $value
     * @return void
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
     *
     * @param string $type
     * @return bool
     */
    public function hasEmailNotification(string $type): bool
    {
        return $this->notification_settings['email'][$type] ?? true;
    }

    /**
     * Check if user has push notification enabled for specific type
     *
     * @param string $type
     * @return bool
     */
    public function hasPushNotification(string $type): bool
    {
        return $this->notification_settings['push'][$type] ?? true;
    }

    /**
     * Check if user has in-app notification enabled for specific type
     *
     * @param string $type
     * @return bool
     */
    public function hasInAppNotification(string $type): bool
    {
        return $this->notification_settings['in_app'][$type] ?? true;
    }

    /**
     * Update user's notification settings
     *
     * @param array $settings
     * @return bool
     */
    public function updateNotificationSettings(array $settings): bool
    {
        $currentSettings = $this->notification_settings;
        $mergedSettings = array_merge($currentSettings, $settings);

        return $this->update(['notification_settings' => $mergedSettings]);
    }

    /**
     * Enable specific notification type
     *
     * @param string $channel
     * @param string $type
     * @return bool
     */
    public function enableNotification(string $channel, string $type): bool
    {
        $settings = $this->notification_settings;
        $settings[$channel][$type] = true;
        return $this->update(['notification_settings' => $settings]);
    }

    /**
     * Disable specific notification type
     *
     * @param string $channel
     * @param string $type
     * @return bool
     */
    public function disableNotification(string $channel, string $type): bool
    {
        $settings = $this->notification_settings;
        $settings[$channel][$type] = false;
        return $this->update(['notification_settings' => $settings]);
    }

    /**
     * Check if user can receive notifications
     *
     * @return bool
     */
    public function canReceiveNotifications(): bool
    {
        return $this->isActive();
    }

    /**
     * Check if user can send messages
     *
     * @return bool
     */
    public function canSendMessages(): bool
    {
        return $this->isActive();
    }

    /**
     * Check if user has verified email
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if user has verified phone
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Get user's profile completion percentage
     *
     * @return int
     */
    public function getProfileCompletionPercentageAttribute(): int
    {
        $fields = [
            'name' => !empty($this->name),
            'email' => !empty($this->email) && $this->hasVerifiedEmail(),
            'phone' => !empty($this->phone) && $this->hasVerifiedPhone(),
            'company_name' => !empty($this->company_name),
            'company_address' => !empty($this->company_address),
            'avatar' => !empty($this->avatar),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Get user's display name with role badge
     *
     * @return string
     */
    public function getDisplayNameWithRoleAttribute(): string
    {
        $roleBadge = match ($this->role) {
            self::ROLE_SUPER_ADMIN => '<span class="badge badge-super-admin">Super Admin</span>',
            self::ROLE_ADMIN => '<span class="badge badge-admin">Admin</span>',
            self::ROLE_USER => '<span class="badge badge-user">User</span>',
            default => '<span class="badge badge-unknown">Unknown</span>',
        };

        return $this->name . ' ' . $roleBadge;
    }

    /**
     * Get user's safe data for public display
     *
     * @return array
     */
    public function getPublicDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'initials' => $this->initials,
            'company_name' => $this->company_name,
            'role' => $this->role,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'is_online' => $this->isOnline(),
            'last_seen' => $this->last_activity_at?->diffForHumans() ?? $this->last_login_at?->diffForHumans(),
            'avatar_color' => $this->avatar_color,
            'avatar_url' => $this->avatar_url,
        ];
    }

    /**
     * Get user's online status for display
     *
     * @return string
     */
    public function getOnlineStatusDisplayAttribute(): string
    {
        if ($this->isOnline()) {
            return 'Online';
        }

        if ($this->last_activity_at) {
            return 'Terakhir dilihat ' . $this->last_activity_at->diffForHumans();
        }

        if ($this->last_login_at) {
            return 'Terakhir login ' . $this->last_login_at->diffForHumans();
        }

        return 'Offline';
    }

    /**
     * Get user's availability status
     *
     * @return string
     */
    public function getAvailabilityStatusAttribute(): string
    {
        if ($this->isOnline()) {
            return 'available';
        }

        return 'away';
    }

    /**
     * Check if user can be contacted (online and active)
     *
     * @return bool
     */
    public function isContactable(): bool
    {
        return $this->isOnline() && $this->isActive();
    }

    /**
     * Send a notification to this user
     *
     * @param string $title
     * @param string $message
     * @param string $type
     * @param array $data
     * @return void
     */
    public function sendNotification(string $title, string $message, string $type = 'info', array $data = []): void
    {
        if (!$this->canReceiveNotifications()) {
            return;
        }

        $notification = [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'user_id' => $this->id,
            'timestamp' => now()->toISOString(),
            'data' => $data,
        ];

        // Send via broadcast
        broadcast(new \App\Events\NotificationCreated($this, $notification))->toOthers();

        // Send via Laravel notifications if needed
        // $this->notify(new \App\Notifications\CustomNotification($notification));
    }

    /**
     * Send a message notification to this user
     *
     * @param Message $message
     * @return void
     */
    public function sendMessageNotification(Message $message): void
    {
        if (!$this->canReceiveNotifications() || !$this->hasInAppNotification(self::NOTIFICATION_MESSAGES)) {
            return;
        }

        $conversation = $message->conversation;
        $sender = $message->sender;

        $notification = [
            'title' => 'Pesan Baru dari ' . $sender->name,
            'message' => Str::limit($message->content, 100),
            'type' => 'message',
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'sender_id' => $sender->id,
            'timestamp' => now()->toISOString(),
        ];

        broadcast(new \App\Events\NotificationCreated($this, $notification))->toOthers();
    }

    /**
     * Get user's conversation with other user
     *
     * @param User $otherUser
     * @return Conversation|null
     */
    public function conversationWith(User $otherUser): ?Conversation
    {
        return Conversation::where(function ($query) use ($otherUser) {
            $query->where('user1_id', $this->id)
                ->where('user2_id', $otherUser->id);
        })->orWhere(function ($query) use ($otherUser) {
            $query->where('user1_id', $otherUser->id)
                ->where('user2_id', $this->id);
        })->first();
    }

    /**
     * Start a new conversation with another user
     *
     * @param User $otherUser
     * @param string|null $message
     * @param int|null $orderId
     * @return Conversation
     */
    public function startConversationWith(User $otherUser, ?string $message = null, ?int $orderId = null): Conversation
    {
        $conversation = $this->conversationWith($otherUser);

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $this->id,
                'user2_id' => $otherUser->id,
                'order_id' => $orderId,
                'last_message_at' => now(),
            ]);
        }

        if ($message) {
            $conversation->messages()->create([
                'sender_id' => $this->id,
                'content' => $message,
            ]);

            $conversation->update(['last_message_at' => now()]);
        }

        return $conversation->fresh(['user1', 'user2', 'lastMessage']);
    }

    /**
     * Get user's unread conversations count
     *
     * @return int
     */
    public function getUnreadConversationsCountAttribute(): int
    {
        return $this->conversations()
            ->whereHas('messages', function ($query) {
                $query->where('sender_id', '!=', $this->id)
                    ->whereNull('read_at');
            })
            ->count();
    }

    /**
     * Get user's unread messages count in specific conversation
     *
     * @param int $conversationId
     * @return int
     */
    public function getUnreadMessagesCountInConversation(int $conversationId): int
    {
        return Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $this->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get user's recent conversations with unread counts and last message
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentConversations(int $limit = 10)
    {
        return $this->conversations()
            ->with(['user1', 'user2', 'lastMessage'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_id', '!=', $this->id)
                    ->whereNull('read_at');
            }])
            ->orderBy('last_message_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($conversation) {
                $conversation->other_user = $conversation->user1_id == $this->id
                    ? $conversation->user2
                    : $conversation->user1;
                return $conversation;
            });
    }

    /**
     * Get user's active conversations (with messages in last 30 days)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveConversations()
    {
        return $this->conversations()
            ->whereHas('messages', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->with(['user1', 'user2', 'lastMessage'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_id', '!=', $this->id)
                    ->whereNull('read_at');
            }])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) {
                $conversation->other_user = $conversation->user1_id == $this->id
                    ? $conversation->user2
                    : $conversation->user1;
                return $conversation;
            });
    }

    /**
     * Get user's conversation partners
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConversationPartners()
    {
        $conversations = $this->conversations()
            ->with(['user1', 'user2'])
            ->get();

        $partners = collect();

        foreach ($conversations as $conversation) {
            $partner = $conversation->user1_id == $this->id
                ? $conversation->user2
                : $conversation->user1;

            if ($partner && !$partners->contains('id', $partner->id)) {
                $partner->last_conversation_at = $conversation->last_message_at;
                $partner->unread_count = $conversation->messages()
                    ->where('sender_id', '!=', $this->id)
                    ->whereNull('read_at')
                    ->count();
                $partners->push($partner);
            }
        }

        return $partners->sortByDesc('last_conversation_at');
    }

    /**
     * Get user's unread messages count
     *
     * @return int
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
     *
     * @return \Illuminate\Database\Eloquent\Collection
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
     * Get user's dashboard statistics
     *
     * @return array
     */
    public function getDashboardStatsAttribute(): array
    {
        $stats = [
            'total_orders' => $this->orders()->count(),
            'pending_orders' => $this->pendingOrders()->count(),
            'confirmed_orders' => $this->confirmedOrders()->count(),
            'in_progress_orders' => $this->inProgressOrders()->count(),
            'completed_orders' => $this->completedOrders()->count(),
            'cancelled_orders' => $this->cancelledOrders()->count(),
            'draft_orders' => $this->draftOrders()->count(),
            'total_spending' => $this->total_spending,
            'average_rating' => $this->average_rating,
            'unread_messages' => $this->unread_messages_count,
            'unread_conversations' => $this->unread_conversations_count,
        ];

        if ($this->isAdmin()) {
            $stats['total_users'] = User::regular()->count();
            $stats['total_feedbacks'] = Feedback::count();
            $stats['pending_feedbacks'] = Feedback::where('status', Feedback::STATUS_PENDING)->count();
            $stats['online_users'] = User::online()->count();
            $stats['offline_users'] = User::offline()->count();
        }

        return $stats;
    }

    /**
     * Get user's activity timeline
     *
     * @return \Illuminate\Support\Collection
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
                    'icon' => 'shopping-bag',
                    'color' => 'blue',
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
                    'icon' => 'message-square',
                    'color' => 'green',
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
                    'icon' => 'send',
                    'color' => 'purple',
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
     *
     * @return string
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
     *
     * @return bool
     */
    public function hasUnreadNotifications(): bool
    {
        return $this->unreadNotifications()->exists();
    }

    /**
     * Get user's unread notifications count
     *
     * @return int
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Mark all notifications as read
     *
     * @return void
     */
    public function markAllNotificationsAsRead(): void
    {
        $this->unreadNotifications->markAsRead();
    }

    /**
     * Scope for active users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for admin users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope for super admin users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuperAdmin($query)
    {
        return $query->where('role', self::ROLE_SUPER_ADMIN);
    }

    /**
     * Scope for regular users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegular($query)
    {
        return $query->where('role', self::ROLE_USER);
    }

    /**
     * Scope for suspended users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    /**
     * Scope for pending users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for users with verified email
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope for online users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true)
            ->orWhere('last_activity_at', '>=', now()->subMinutes(5));
    }

    /**
     * Scope for offline users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOffline($query)
    {
        return $query->where('is_online', false)
            ->where(function ($q) {
                $q->whereNull('last_activity_at')
                    ->orWhere('last_activity_at', '<', now()->subMinutes(5));
            });
    }

    /**
     * Relationship with orders (user can have many orders)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relationship with feedbacks (user can have many feedbacks)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Relationship with progress updates (admin can update progress)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'updated_by');
    }

    /**
     * Relationship with uploaded files (admin can upload files)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploadedFiles()
    {
        return $this->hasMany(OrderFile::class, 'uploaded_by');
    }

    /**
     * Relationship with feedback replies (admin can reply to feedback)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedbackReplies()
    {
        return $this->hasMany(Feedback::class, 'responded_by');
    }

    /**
     * Relationship with sent messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Relationship with conversations as user1
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    /**
     * Relationship with conversations as user2
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    /**
     * Get all conversations for this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conversations()
    {
        return Conversation::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id)
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * Get user's pending orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pendingOrders()
    {
        return $this->orders()->where('status', Order::STATUS_PENDING);
    }

    /**
     * Get user's confirmed orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function confirmedOrders()
    {
        return $this->orders()->where('status', Order::STATUS_CONFIRMED);
    }

    /**
     * Get user's in-progress orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inProgressOrders()
    {
        return $this->orders()->where('status', Order::STATUS_IN_PROGRESS);
    }

    /**
     * Get user's completed orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function completedOrders()
    {
        return $this->orders()->where('status', Order::STATUS_COMPLETED);
    }

    /**
     * Get user's cancelled orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cancelledOrders()
    {
        return $this->orders()->where('status', Order::STATUS_CANCELLED);
    }

    /**
     * Get user's draft orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function draftOrders()
    {
        return $this->orders()->where('status', Order::STATUS_DRAFT);
    }

    /**
     * Get user's total spending
     *
     * @return float
     */
    public function getTotalSpendingAttribute(): float
    {
        return (float) $this->orders()
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('total_price');
    }

    /**
     * Get user's display total spending
     *
     * @return string
     */
    public function getDisplayTotalSpendingAttribute(): string
    {
        return 'Rp ' . number_format($this->total_spending, 0, ',', '.');
    }

    /**
     * Get user's average rating from feedbacks
     *
     * @return float|null
     */
    public function getAverageRatingAttribute(): ?float
    {
        $average = $this->feedbacks()->avg('rating');
        return $average ? round((float) $average, 1) : null;
    }

    /**
     * Get user's orders count by status
     *
     * @return array
     */
    public function getOrdersCountByStatusAttribute(): array
    {
        return [
            'total' => $this->orders()->count(),
            'pending' => $this->pendingOrders()->count(),
            'confirmed' => $this->confirmedOrders()->count(),
            'in_progress' => $this->inProgressOrders()->count(),
            'completed' => $this->completedOrders()->count(),
            'cancelled' => $this->cancelledOrders()->count(),
            'draft' => $this->draftOrders()->count(),
        ];
    }

    /**
     * Check if user can create order
     *
     * @return bool
     */
    public function canCreateOrder(): bool
    {
        return $this->isActive() && $this->isUser();
    }

    /**
     * Check if user can manage orders (admin only)
     *
     * @return bool
     */
    public function canManageOrders(): bool
    {
        return $this->isAdmin() && $this->isActive();
    }

    /**
     * Check if user can manage users (admin only)
     *
     * @return bool
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin() && $this->isActive();
    }

    /**
     * Check if user can access admin panel
     *
     * @return bool
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->isAdmin() && $this->isActive();
    }

    /**
     * Compatibility method for initials (for backward compatibility)
     *
     * @return string
     */
    public function initials(): string
    {
        return $this->initials;
    }
}
