<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Package;
use App\Models\Order;

class OrderLanding extends Component
{
    public $selectedPackage = null;
    public $projectName = '';
    public $domainName = '';
    public $specialRequirements = '';

    public $packages = [];
    public $selectedPackageData = null;

    // Step management
    public $currentStep = 1;
    public $steps = [
        1 => 'Pilih Paket',
        2 => 'Konfigurasi',
        3 => 'Pembayaran',
        4 => 'Selesai'
    ];

    protected $rules = [
        'projectName' => 'required|min:3|max:255',
        'domainName' => 'required|min:3|max:50|regex:/^[a-zA-Z0-9-]+$/',
        'selectedPackage' => 'required|exists:packages,id',
    ];

    protected $messages = [
        'projectName.required' => 'Nama proyek wajib diisi.',
        'projectName.min' => 'Nama proyek minimal 3 karakter.',
        'domainName.required' => 'Domain wajib diisi.',
        'domainName.min' => 'Domain minimal 3 karakter.',
        'domainName.regex' => 'Domain hanya boleh berisi huruf, angka, dan tanda hubung.',
    ];

    public function mount($package = null)
    {
        // Load semua package aktif
        $this->packages = Package::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        // Hanya set package otomatis jika datang dari daftar paket (ada parameter package)
        if ($package) {
            $packageData = collect($this->packages)->firstWhere('id', $package);
            if ($packageData) {
                $this->selectedPackage = $package;
                $this->selectedPackageData = $packageData;
                $this->currentStep = 2; // Langsung ke step konfigurasi
            }
        }
    }

    public function updatedSelectedPackage()
    {
        $this->updateSelectedPackageData();
        if ($this->selectedPackage) {
            $this->currentStep = 2; // Pindah ke step konfigurasi
        }
    }

    protected function updateSelectedPackageData()
    {
        if ($this->selectedPackage) {
            $this->selectedPackageData = collect($this->packages)->firstWhere('id', $this->selectedPackage);
        } else {
            $this->selectedPackageData = null;
        }
    }

    public function getBasePriceProperty()
    {
        if (!$this->selectedPackageData) {
            return 0;
        }

        return $this->selectedPackageData['base_price'] ?? 0;
    }

    public function getTotalPriceProperty()
    {
        if (!$this->selectedPackageData) {
            return 0;
        }

        return $this->basePrice;
    }

    public function getCanProceedProperty()
    {
        // Perbaikan: Gunakan trim() hanya jika string tidak null
        $projectName = trim($this->projectName ?? '');
        $domainName = trim($this->domainName ?? '');

        return !empty($projectName) &&
               !empty($domainName) &&
               !empty($this->selectedPackage);
    }

    public function goToStep($step)
    {
        if ($step == 1 || ($step == 2 && $this->selectedPackage)) {
            $this->currentStep = $step;
        }
    }

    public function processOrder()
    {
        // Validasi
        $this->validate();

        try {
            // Prepare special requirements
            $specialRequirements = [];
            if (!empty(trim($this->specialRequirements))) {
                $specialRequirements = ['kebutuhan_khusus' => $this->specialRequirements];
            }

            // Get package data
            $package = Package::findOrFail($this->selectedPackage);

            // Generate order number
            $orderNumber = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Prepare order data
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'package_id' => $this->selectedPackage,
                'project_name' => $this->projectName,
                'domain_name' => $this->domainName,
                'special_requirements' => $specialRequirements,
                'base_price' => $this->basePrice,
                'discount_amount' => 0,
                'total_price' => $this->totalPrice,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'admin_notes' => 'Order dibuat melalui landing page',
            ];

            // Create order
            $order = Order::create($orderData);

            // Jika paket custom price, langsung redirect ke detail order
            if ($package->is_custom_price) {
                Session::flash('success', 'Pesanan custom berhasil dibuat! Tim kami akan menghubungi Anda untuk konsultasi.');

                return redirect()->route('user.order-detail', ['order' => $order->id])
                    ->with('success', 'Pesanan custom berhasil dibuat! Tim kami akan menghubungi Anda untuk konsultasi.');
            }

            // Redirect to payment page untuk paket reguler - PERBAIKAN DI SINI
            return redirect()->route('payment.page', ['order' => $order->id])
                ->with('success', 'Pesanan berhasil dibuat! Silakan lanjutkan pembayaran.');

        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
            $this->dispatch('order-failed', [
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function saveDraft()
    {
        $this->validate([
            'projectName' => 'nullable|min:3|max:255',
            'domainName' => 'nullable|min:3|max:50|regex:/^[a-zA-Z0-9-]+$/',
        ]);

        try {
            // Prepare special requirements
            $specialRequirements = [];
            if (!empty(trim($this->specialRequirements))) {
                $specialRequirements = ['kebutuhan_khusus' => $this->specialRequirements];
            }

            // Generate order number
            $orderNumber = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Prepare order data
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'package_id' => $this->selectedPackage,
                'project_name' => $this->projectName,
                'domain_name' => $this->domainName,
                'special_requirements' => $specialRequirements,
                'base_price' => $this->basePrice,
                'discount_amount' => 0,
                'total_price' => $this->totalPrice,
                'status' => Order::STATUS_DRAFT,
                'payment_status' => Order::PAYMENT_PENDING,
                'admin_notes' => 'Draft order dibuat melalui landing page',
            ];

            // Create draft order
            $order = Order::create($orderData);

            Session::flash('success', 'Pesanan disimpan sebagai draft.');

            $this->dispatch('draft-saved', [
                'message' => 'Pesanan berhasil disimpan sebagai draft. No: ' . $order->order_number
            ]);

        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan saat menyimpan draft.');
            $this->dispatch('draft-failed', [
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.order-landing');
    }
}
