<?php

namespace App\Livewire\Admin;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $selectedFeedback = null;
    public $responseMessage = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        // Inisialisasi komponen
    }

    public function getTotalFeedbacksProperty()
    {
        return Feedback::count();
    }

    public function getUnreadFeedbacksProperty()
    {
        return Feedback::where('status', 'pending')->count();
    }

    public function getFeedbacksProperty()
    {
        return Feedback::query()
            ->with(['user', 'order', 'responder'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('message', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('order', function ($orderQuery) {
                          $orderQuery->where('order_number', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter, function ($query) {
                match ($this->statusFilter) {
                    'unread' => $query->where('status', 'pending'),
                    'read' => $query->where('status', 'read'),
                    'responded' => $query->whereNotNull('response'),
                    default => $query,
                };
            })
            ->latest()
            ->paginate(10);
    }

    public function markAsRead($feedbackId)
    {
        try {
            $feedback = Feedback::findOrFail($feedbackId);
            $feedback->update([
                'status' => 'read',
                'updated_at' => now(),
            ]);

            session()->flash('message', 'Feedback telah ditandai sebagai sudah dibaca.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menandai feedback sebagai sudah dibaca.');
        }
    }

    public function markAsUnread($feedbackId)
    {
        try {
            $feedback = Feedback::findOrFail($feedbackId);
            $feedback->update([
                'status' => 'pending',
                'updated_at' => now(),
            ]);

            session()->flash('message', 'Feedback telah ditandai sebagai belum dibaca.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menandai feedback sebagai belum dibaca.');
        }
    }

    public function respondToFeedback($feedbackId)
    {
        $this->selectedFeedback = Feedback::with('user')->find($feedbackId);
        $this->responseMessage = '';
    }

    public function sendResponse()
    {
        $this->validate([
            'responseMessage' => 'required|string|min:5|max:1000',
        ]);

        try {
            $this->selectedFeedback->update([
                'response' => $this->responseMessage,
                'responded_by' => Auth::id(),
                'responded_at' => now(),
                'status' => 'read',
                'updated_at' => now(),
            ]);

            session()->flash('message', 'Balasan telah dikirim ke user.');
            $this->selectedFeedback = null;
            $this->responseMessage = '';
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim balasan.');
        }
    }

    public function deleteFeedback($feedbackId)
    {
        try {
            Feedback::findOrFail($feedbackId)->delete();
            session()->flash('message', 'Feedback berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus feedback.');
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'typeFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.feedback-management', [
            'feedbacks' => $this->feedbacks,
            'totalFeedbacks' => $this->totalFeedbacks,
            'unreadFeedbacks' => $this->unreadFeedbacks,
        ]);
    }
}
