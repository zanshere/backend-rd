<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        // Inisialisasi jika diperlukan
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function getUsersProperty()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('status', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('status', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function toggleRole($userId)
    {
        try {
            DB::transaction(function () use ($userId) {
                $user = User::findOrFail($userId);
                $user->role = $user->role === 'admin' ? 'user' : 'admin';
                $user->save();
            });

            session()->flash('message', 'Role user berhasil diubah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah role user: ' . $e->getMessage());
        }
    }

    public function toggleStatus($userId)
    {
        try {
            DB::transaction(function () use ($userId) {
                $user = User::findOrFail($userId);
                $user->status = !$user->status;
                $user->save();
            });

            session()->flash('message', 'Status user berhasil diubah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }

    public function confirmDelete($userId)
    {
        try {
            DB::transaction(function () use ($userId) {
                $user = User::findOrFail($userId);

                // Cek jika user sedang login
                if ($user->id === auth()->id()) {
                    throw new \Exception('Tidak dapat menghapus akun sendiri.');
                }

                $user->delete();
            });

            session()->flash('message', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => $this->users,
        ]);
    }
}
