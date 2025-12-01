<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentSuccess extends Component
{
    public $order;
    public $orderId;
    public $isPolling = false;
    public $progressPercentage = 0;
    public $timelineItems = [];

    protected $listeners = [
        'refresh' => 'loadOrder',
        'stopPolling' => 'stopPollingHandler'
    ];

    public function mount($order)
    {
        $this->orderId = $order;
        $this->loadOrder();

        // Start polling untuk cek status konfirmasi admin
        if ($this->order->payment_status === Order::PAYMENT_PAID &&
            $this->order->status === Order::STATUS_PENDING) {
            $this->isPolling = true;
        }
    }

    public function loadOrder()
    {
        $this->order = Order::with(['package', 'user'])
            ->where('id', $this->orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Jika payment belum success, redirect ke payment page
        if ($this->order->payment_status !== Order::PAYMENT_PAID) {
            return redirect()->route('payment.page', ['order' => $this->orderId]);
        }

        // Update progress percentage berdasarkan status
        $this->updateProgressPercentage();

        // Update timeline items
        $this->updateTimelineItems();

        // Jika sudah dikonfirmasi admin, stop polling
        if ($this->order->status === Order::STATUS_CONFIRMED) {
            $this->isPolling = false;
        }
    }

    protected function updateProgressPercentage()
    {
        // Progress berdasarkan status order
        switch ($this->order->status) {
            case Order::STATUS_DRAFT:
                $this->progressPercentage = 0;
                break;
            case Order::STATUS_PENDING:
                $this->progressPercentage = 10; // Menunggu konfirmasi admin
                break;
            case Order::STATUS_CONFIRMED:
                $this->progressPercentage = 20; // Sudah dikonfirmasi, belum mulai pengerjaan
                break;
            case Order::STATUS_IN_PROGRESS:
                // Jika ada progress updates, gunakan itu
                if ($this->order->progressUpdates()->exists()) {
                    $latestProgress = $this->order->progressUpdates()->latest()->first();
                    $this->progressPercentage = $latestProgress->progress_percentage;
                } else {
                    $this->progressPercentage = 30; // Default saat mulai pengerjaan
                }
                break;
            case Order::STATUS_COMPLETED:
                $this->progressPercentage = 100;
                break;
            case Order::STATUS_CANCELLED:
                $this->progressPercentage = 0;
                break;
            default:
                $this->progressPercentage = 0;
        }
    }

    protected function updateTimelineItems()
    {
        $this->timelineItems = [
            [
                'title' => 'Pesanan Dibuat',
                'description' => 'Pesanan berhasil direkam di sistem',
                'date' => $this->order->created_at->format('d M Y H:i'),
                'icon' => 'shopping-cart',
                'color' => 'blue',
                'completed' => true,
                'current' => false
            ],
            [
                'title' => 'Pembayaran Berhasil',
                'description' => 'Pembayaran telah diterima dan diverifikasi',
                'date' => $this->order->paid_at ? $this->order->paid_at->format('d M Y H:i') : now()->format('d M Y H:i'),
                'icon' => 'credit-card',
                'color' => 'green',
                'completed' => true,
                'current' => false
            ]
        ];

        if ($this->order->status === Order::STATUS_PENDING) {
            $this->timelineItems[] = [
                'title' => 'Menunggu Konfirmasi Admin',
                'description' => 'Pesanan sedang menunggu verifikasi dan konfirmasi dari admin',
                'date' => 'Proses 1x24 jam',
                'icon' => 'clock',
                'color' => 'yellow',
                'completed' => false,
                'current' => true
            ];
            $this->timelineItems[] = [
                'title' => 'Pesanan Dikonfirmasi',
                'description' => 'Admin akan memverifikasi dan mengkonfirmasi pesanan Anda',
                'date' => 'Setelah konfirmasi admin',
                'icon' => 'check-circle',
                'color' => 'gray',
                'completed' => false,
                'current' => false
            ];
        } elseif ($this->order->status === Order::STATUS_CONFIRMED) {
            $this->timelineItems[] = [
                'title' => 'Pesanan Dikonfirmasi',
                'description' => 'Admin telah mengkonfirmasi dan memverifikasi pesanan Anda',
                'date' => $this->order->updated_at->format('d M Y H:i'),
                'icon' => 'check-circle',
                'color' => 'emerald',
                'completed' => true,
                'current' => false
            ];
            $this->timelineItems[] = [
                'title' => 'Persiapan Proyek',
                'description' => 'Tim sedang mempersiapkan proyek Anda',
                'date' => 'Akan segera dimulai',
                'icon' => 'package',
                'color' => 'purple',
                'completed' => false,
                'current' => true
            ];
        }

        // Tambahkan progress updates jika ada
        if ($this->order->progressUpdates()->exists()) {
            foreach ($this->order->progressUpdates()->orderBy('created_at', 'asc')->get() as $progress) {
                $this->timelineItems[] = [
                    'title' => 'Progress: ' . $progress->progress_label,
                    'description' => $progress->notes ?? 'Update progress pengerjaan',
                    'date' => $progress->created_at->format('d M Y H:i'),
                    'icon' => 'activity',
                    'color' => 'indigo',
                    'completed' => true,
                    'current' => false
                ];
            }
        }

        // Jika order sudah selesai
        if ($this->order->status === Order::STATUS_COMPLETED) {
            $this->timelineItems[] = [
                'title' => 'Proyek Selesai',
                'description' => 'Website telah selesai dan siap digunakan',
                'date' => $this->order->updated_at->format('d M Y H:i'),
                'icon' => 'check-circle-2',
                'color' => 'green',
                'completed' => true,
                'current' => false
            ];
        }
    }

    public function checkStatus()
    {
        $this->loadOrder();

        // Jika sudah dikonfirmasi admin, stop polling
        if ($this->order->status === Order::STATUS_CONFIRMED) {
            $this->isPolling = false;
            $this->dispatch('order-confirmed');
        }
    }

    public function stopPollingHandler()
    {
        $this->isPolling = false;
    }

    public function startPolling()
    {
        if (!$this->isPolling) {
            $this->isPolling = true;
        }
    }

    public function stopPolling()
    {
        $this->isPolling = false;
    }

    public function downloadInvoice()
    {
        $this->dispatch('download-started');

        return response()->streamDownload(function () {
            echo "=== INVOICE PEMBAYARAN ===\n\n";
            echo "No. Invoice: INV-" . $this->order->order_number . "\n";
            echo "No. Order: " . $this->order->order_number . "\n";
            echo "Tanggal: " . $this->order->created_at->format('d M Y H:i') . "\n";
            echo "Status Pembayaran: " . $this->order->payment_status_display_name . "\n";
            echo "Status Pesanan: " . $this->order->status_display_name . "\n\n";
            echo "--- Detail Pesanan ---\n";
            echo "Paket: " . $this->order->package->name . "\n";
            echo "Project: " . $this->order->project_name . "\n";
            echo "Domain: " . $this->order->domain_name . "\n\n";
            echo "--- Rincian Biaya ---\n";
            echo "Harga Paket: Rp " . number_format($this->order->base_price, 0, ',', '.') . "\n";
            if ($this->order->discount_amount > 0) {
                echo "Diskon: -Rp " . number_format($this->order->discount_amount, 0, ',', '.') . "\n";
            }
            echo "Total: Rp " . number_format($this->order->total_price, 0, ',', '.') . "\n\n";
            echo "--- Informasi Pelanggan ---\n";
            echo "Nama: " . $this->order->user->name . "\n";
            echo "Email: " . $this->order->user->email . "\n";
            echo "\nTerima kasih telah menggunakan layanan kami.\n";
            echo "Support: support@yourdomain.com | 0812-3456-7890\n";
        }, 'invoice-' . $this->order->order_number . '.txt');
    }

    public function render()
    {
        return view('livewire.payment-success');
    }
}
