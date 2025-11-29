<?php

namespace App\Livewire\Common;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Notifications extends Component
{
    use WithPagination;

    public $typeFilter = 'all';
    public $statusFilter = 'all';
    public $dateFilter = 'all';

    public $emailSettings = [];
    public $pushSettings = [];

    protected $listeners = [
        'refreshNotifications' => '$refresh',
    ];

    public function mount()
    {
        $this->loadUserSettings();
    }

    private function loadUserSettings()
    {
        /** @var User $user */
        $user = Auth::user();

        // Default settings if none exist
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

        $userSettings = $user->notification_settings ?? $defaultSettings;

        $this->emailSettings = [
            'order_updates' => [
                'label' => 'Update Pesanan',
                'description' => 'Notifikasi tentang status pesanan',
                'enabled' => $userSettings['email']['order_updates'] ?? true,
            ],
            'messages' => [
                'label' => 'Pesan Baru',
                'description' => 'Notifikasi ketika ada pesan baru',
                'enabled' => $userSettings['email']['messages'] ?? true,
            ],
            'system' => [
                'label' => 'Sistem',
                'description' => 'Notifikasi penting dari sistem',
                'enabled' => $userSettings['email']['system'] ?? true,
            ],
        ];

        $this->pushSettings = [
            'order_updates' => [
                'label' => 'Update Pesanan',
                'description' => 'Notifikasi tentang status pesanan',
                'enabled' => $userSettings['push']['order_updates'] ?? true,
            ],
            'messages' => [
                'label' => 'Pesan Baru',
                'description' => 'Notifikasi ketika ada pesan baru',
                'enabled' => $userSettings['push']['messages'] ?? true,
            ],
            'promotions' => [
                'label' => 'Promosi',
                'description' => 'Penawaran dan promosi khusus',
                'enabled' => $userSettings['push']['promotions'] ?? false,
            ],
        ];
    }

    public function getNotificationsProperty(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = Auth::user();
        $query = $user->notifications();

        if ($this->typeFilter !== 'all') {
            $query->where('type', 'like', '%' . $this->typeFilter . '%');
        }

        if ($this->statusFilter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->statusFilter === 'read') {
            $query->whereNotNull('read_at');
        }

        $this->applyDateFilter($query);

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    private function applyDateFilter($query)
    {
        switch ($this->dateFilter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
        }
    }

    public function getTotalCountProperty(): int
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->notifications()->count();
    }

    public function getUnreadCountProperty(): int
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->unreadNotifications()->count();
    }

    public function getTodayCountProperty(): int
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->notifications()
            ->whereDate('created_at', today())
            ->count();
    }

    public function getWeekCountProperty(): int
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->notifications()
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    public function markAsRead(string $notificationId)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $notificationId)->first();

            if ($notification && !$notification->read_at) {
                $notification->markAsRead();
                session()->flash('message', 'Notifikasi ditandai sebagai sudah dibaca.');
                $this->dispatch('refreshNotifications');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menandai notifikasi sebagai sudah dibaca.');
        }
    }

    public function markAllAsRead()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();
            session()->flash('message', 'Semua notifikasi ditandai sebagai sudah dibaca.');
            $this->dispatch('refreshNotifications');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menandai semua notifikasi sebagai sudah dibaca.');
        }
    }

    public function deleteNotification(string $notificationId)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $user->notifications()
                ->where('id', $notificationId)
                ->delete();

            session()->flash('message', 'Notifikasi berhasil dihapus.');
            $this->dispatch('refreshNotifications');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus notifikasi.');
        }
    }

    public function clearAll()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $user->notifications()->delete();
            session()->flash('message', 'Semua notifikasi berhasil dihapus.');
            $this->dispatch('refreshNotifications');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus semua notifikasi.');
        }
    }

    public function toggleEmailSetting(string $key)
    {
        if (isset($this->emailSettings[$key])) {
            $this->emailSettings[$key]['enabled'] = !$this->emailSettings[$key]['enabled'];
        }
    }

    public function togglePushSetting(string $key)
    {
        if (isset($this->pushSettings[$key])) {
            $this->pushSettings[$key]['enabled'] = !$this->pushSettings[$key]['enabled'];
        }
    }

    public function saveSettings()
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $settings = [
                'email' => [
                    'order_updates' => $this->emailSettings['order_updates']['enabled'] ?? true,
                    'messages' => $this->emailSettings['messages']['enabled'] ?? true,
                    'system' => $this->emailSettings['system']['enabled'] ?? true,
                ],
                'push' => [
                    'order_updates' => $this->pushSettings['order_updates']['enabled'] ?? true,
                    'messages' => $this->pushSettings['messages']['enabled'] ?? true,
                    'promotions' => $this->pushSettings['promotions']['enabled'] ?? false,
                ],
            ];

            $user->update([
                'notification_settings' => $settings,
            ]);

            session()->flash('message', 'Pengaturan notifikasi berhasil disimpan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan pengaturan notifikasi.');
        }
    }

    public function resetFilters()
    {
        $this->reset(['typeFilter', 'statusFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.common.notifications', [
            'notifications' => $this->notifications,
            'totalCount' => $this->totalCount,
            'unreadCount' => $this->unreadCount,
            'todayCount' => $this->todayCount,
            'weekCount' => $this->weekCount,
        ]);
    }
}
