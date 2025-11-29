<?php

namespace App\Livewire\User;

use App\Models\Feedback as FeedbackModel;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Feedback extends Component
{
    use WithPagination;

    public $type = 'suggestion';
    public $orderId = '';
    public $message = '';
    public $rating = 0;

    public $feedbackTypes = [
        'suggestion' => 'Saran',
        'complaint' => 'Keluhan',
        'bug' => 'Bug/Error',
        'feature' => 'Permintaan Fitur',
        'other' => 'Lainnya'
    ];

    protected $rules = [
        'type' => 'required|in:suggestion,complaint,bug,feature,other',
        'orderId' => 'nullable|exists:orders,id',
        'message' => 'required|string|min:10|max:1000',
        'rating' => 'nullable|integer|min:0|max:5',
    ];

    protected $messages = [
        'type.required' => 'Tipe feedback harus dipilih.',
        'type.in' => 'Tipe feedback tidak valid.',
        'orderId.exists' => 'Pesanan tidak valid.',
        'message.required' => 'Pesan feedback harus diisi.',
        'message.min' => 'Pesan feedback minimal 10 karakter.',
        'message.max' => 'Pesan feedback maksimal 1000 karakter.',
        'rating.min' => 'Rating minimal 0.',
        'rating.max' => 'Rating maksimal 5.',
    ];

    public function mount()
    {
        // Inisialisasi komponen
    }

    public function getUserOrdersProperty()
    {
        try {
            return Order::where('user_id', Auth::id())
                ->whereIn('status', ['completed', 'delivered', 'success']) // Hanya pesanan yang sudah selesai
                ->with('package')
                ->latest()
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getRecentFeedbacksProperty()
    {
        try {
            return FeedbackModel::where('user_id', Auth::id())
                ->with('order')
                ->latest()
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getFeedbacksProperty()
    {
        try {
            return FeedbackModel::where('user_id', Auth::id())
                ->with(['order', 'order.package'])
                ->latest()
                ->paginate(10);
        } catch (\Exception $e) {
            return collect()->paginate(10);
        }
    }

    public function submitFeedback()
    {
        $this->validate();

        try {
            FeedbackModel::create([
                'user_id' => Auth::id(),
                'order_id' => $this->orderId ?: null,
                'type' => $this->type,
                'message' => $this->message,
                'rating' => $this->rating ?: null,
                'status' => FeedbackModel::STATUS_PENDING,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Reset form
            $this->reset(['orderId', 'message', 'rating']);
            $this->type = 'suggestion'; // Set default value setelah reset

            session()->flash('message', 'Feedback berhasil dikirim! Terima kasih atas masukan Anda.');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengirim feedback. Silakan coba lagi.');
        }
    }

    public function setRating($score)
    {
        $this->rating = $score;
    }

    public function render()
    {
        return view('livewire.user.feedback', [
            'userOrders' => $this->userOrders,
            'recentFeedbacks' => $this->recentFeedbacks,
            'feedbacks' => $this->feedbacks,
            'feedbackTypes' => $this->feedbackTypes,
        ]);
    }
}
