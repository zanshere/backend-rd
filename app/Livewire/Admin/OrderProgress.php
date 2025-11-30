<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\ProgressUpdate;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderProgress extends Component
{
    public $orderId;
    public $order;
    public $progressPercentage = 0;
    public $progressNotes = '';
    public $progressUpdates = [];
    public $progressUpdate = '';

    public function mount($order)
    {
        $this->orderId = $order;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = Order::with(['user', 'package', 'progressUpdates'])
            ->findOrFail($this->orderId);

        $this->progressUpdates = $this->order->progressUpdates->sortByDesc('created_at');
        $this->progressPercentage = $this->order->progress_percentage ?? 0;
    }

    /**
     * Get progress steps
     */
    public function getProgressStepsProperty(): array
    {
        $currentStatus = $this->order->status;

        $steps = [
            [
                'name' => 'Pembayaran',
                'description' => 'Menunggu konfirmasi pembayaran',
                'completed' => true,
                'current' => false,
            ],
            [
                'name' => 'Konfirmasi',
                'description' => 'Pesanan dikonfirmasi admin',
                'completed' => in_array($currentStatus, [Order::STATUS_CONFIRMED, Order::STATUS_IN_PROGRESS, Order::STATUS_COMPLETED]),
                'current' => $currentStatus === Order::STATUS_PENDING,
            ],
            [
                'name' => 'Pengerjaan',
                'description' => 'Tim kami sedang mengerjakan proyek Anda',
                'completed' => in_array($currentStatus, [Order::STATUS_COMPLETED]),
                'current' => $currentStatus === Order::STATUS_IN_PROGRESS,
            ],
            [
                'name' => 'Selesai',
                'description' => 'Proyek telah selesai',
                'completed' => $currentStatus === Order::STATUS_COMPLETED,
                'current' => false,
            ]
        ];

        return $steps;
    }

    /**
     * Update progress percentage
     */
    public function updateProgress()
    {
        $this->validate([
            'progressPercentage' => 'required|integer|min:0|max:100',
            'progressNotes' => 'nullable|string|max:1000',
        ]);

        try {
            // Create progress update
            $progressUpdate = ProgressUpdate::create([
                'order_id' => $this->orderId,
                'progress_percentage' => $this->progressPercentage,
                'notes' => $this->progressNotes,
                'updated_by' => Auth::id(),
            ]);

            // Update order progress if needed
            if ($this->progressPercentage === 100) {
                $this->order->update([
                    'status' => Order::STATUS_COMPLETED,
                    'progress_percentage' => 100,
                ]);
            } else {
                $this->order->update([
                    'progress_percentage' => $this->progressPercentage,
                ]);

                // Auto update status to in progress if not already
                if ($this->order->status === Order::STATUS_CONFIRMED) {
                    $this->order->update([
                        'status' => Order::STATUS_IN_PROGRESS,
                    ]);
                }
            }

            // Reset form
            $this->progressNotes = '';

            // Reload data
            $this->loadOrder();

            session()->flash('message', 'Progress berhasil diupdate.');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Add progress update
     */
    public function addProgressUpdate()
    {
        $this->validate([
            'progressUpdate' => 'required|string|max:1000',
            'progressPercentage' => 'required|integer|min:0|max:100',
        ]);

        try {
            // Create progress update
            $progressUpdate = ProgressUpdate::create([
                'order_id' => $this->orderId,
                'progress_percentage' => $this->progressPercentage,
                'notes' => $this->progressUpdate,
                'updated_by' => Auth::id(),
            ]);

            // Update order progress
            $this->order->update([
                'progress_percentage' => $this->progressPercentage,
            ]);

            // Auto update status to in progress if not already
            if ($this->order->status === Order::STATUS_CONFIRMED) {
                $this->order->update([
                    'status' => Order::STATUS_IN_PROGRESS,
                ]);
            }

            // Reset form
            $this->progressUpdate = '';

            // Reload data
            $this->loadOrder();

            session()->flash('message', 'Update progress berhasil ditambahkan.');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Complete a step
     */
    public function completeStep($stepIndex)
    {
        $steps = $this->progressSteps;

        if (isset($steps[$stepIndex]) && $steps[$stepIndex]['current']) {
            switch ($stepIndex) {
                case 1: // Konfirmasi step
                    $this->order->update(['status' => Order::STATUS_CONFIRMED]);
                    break;
                case 2: // Pengerjaan step
                    $this->order->update(['status' => Order::STATUS_IN_PROGRESS]);
                    break;
                case 3: // Selesai step
                    $this->order->update([
                        'status' => Order::STATUS_COMPLETED,
                        'progress_percentage' => 100
                    ]);
                    break;
            }

            $this->loadOrder();
            session()->flash('message', 'Step berhasil diselesaikan.');
        }
    }

    /**
     * Update order status
     */
    public function updateStatus($status)
    {
        $validStatuses = [
            'confirmed' => Order::STATUS_CONFIRMED,
            'in_progress' => Order::STATUS_IN_PROGRESS,
            'completed' => Order::STATUS_COMPLETED,
            'cancelled' => Order::STATUS_CANCELLED,
        ];

        if (!array_key_exists($status, $validStatuses)) {
            session()->flash('error', 'Status tidak valid.');
            return;
        }

        $newStatus = $validStatuses[$status];

        // Additional validation for status transitions
        if ($newStatus === Order::STATUS_COMPLETED && $this->order->progress_percentage < 100) {
            session()->flash('error', 'Tidak dapat menyelesaikan order sebelum progress mencapai 100%.');
            return;
        }

        $this->order->update(['status' => $newStatus]);

        // If marking as completed, set progress to 100%
        if ($newStatus === Order::STATUS_COMPLETED) {
            $this->order->update(['progress_percentage' => 100]);

            // Create final progress update
            ProgressUpdate::create([
                'order_id' => $this->orderId,
                'progress_percentage' => 100,
                'notes' => 'Order telah diselesaikan.',
                'updated_by' => Auth::id(),
            ]);
        }

        $this->loadOrder();

        $statusNames = [
            Order::STATUS_CONFIRMED => 'dikonfirmasi',
            Order::STATUS_IN_PROGRESS => 'dalam pengerjaan',
            Order::STATUS_COMPLETED => 'selesai',
            Order::STATUS_CANCELLED => 'dibatalkan',
        ];

        session()->flash('message', "Order berhasil ditandai sebagai {$statusNames[$newStatus]}.");
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->order->status) {
            Order::STATUS_DRAFT => 'gray',
            Order::STATUS_PENDING => 'yellow',
            Order::STATUS_CONFIRMED => 'blue',
            Order::STATUS_IN_PROGRESS => 'indigo',
            Order::STATUS_COMPLETED => 'green',
            Order::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get display status name
     */
    public function getDisplayStatusProperty(): string
    {
        return match($this->order->status) {
            Order::STATUS_DRAFT => 'Draft',
            Order::STATUS_PENDING => 'Menunggu Konfirmasi',
            Order::STATUS_CONFIRMED => 'Dikonfirmasi',
            Order::STATUS_IN_PROGRESS => 'Dalam Pengerjaan',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Check if order can be updated
     */
    public function getCanUpdateProgressProperty(): bool
    {
        return $this->order->isConfirmed() || $this->order->isInProgress();
    }

    public function render()
    {
        return view('livewire.admin.order-progress');
    }
}
