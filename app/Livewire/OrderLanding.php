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
    public $duration = 1;
    public $specialRequirements = '';

    public $addons = [
        'seo' => false,
        'maintenance' => false,
        'analytics' => false,
        'training' => false,
    ];

    public $packages = [];
    public $selectedPackageData = null;

    public $addonPrices = [
        'seo' => 150000,
        'maintenance' => 200000,
        'analytics' => 100000,
        'training' => 500000,
    ];

    public $durationDiscounts = [
        1 => 0,
        3 => 0.05,
        6 => 0.10,
        12 => 0.15,
    ];

    public function mount($package = null)
    {
        // Load semua package aktif
        $this->packages = Package::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('id')
            ->toArray();

        // Set package yang dipilih dari parameter atau default pertama
        if ($package && isset($this->packages[$package])) {
            $this->selectedPackage = $package;
        } elseif (!empty($this->packages)) {
            $this->selectedPackage = array_key_first($this->packages);
        }

        $this->updateSelectedPackageData();

        // You can pre-fill data if user is logged in
        if (Auth::check()) {
            $user = Auth::user();
            // Pre-fill user data if available
        }
    }

    public function updatedSelectedPackage()
    {
        $this->updateSelectedPackageData();
    }

    protected function updateSelectedPackageData()
    {
        if ($this->selectedPackage && isset($this->packages[$this->selectedPackage])) {
            $this->selectedPackageData = $this->packages[$this->selectedPackage];
        } else {
            $this->selectedPackageData = null;
        }
    }

    public function getBasePriceProperty()
    {
        if (!$this->selectedPackageData) {
            return 0;
        }

        return $this->selectedPackageData['base_price'];
    }

    public function getAddonsTotalProperty()
    {
        $total = 0;
        foreach ($this->addons as $addon => $selected) {
            if ($selected && $addon !== 'training') {
                $total += $this->addonPrices[$addon];
            }
        }
        return $total;
    }

    public function getDiscountAmountProperty()
    {
        $monthlySubtotal = $this->basePrice + $this->addonsTotal;
        $subtotal = $monthlySubtotal * $this->duration;
        return $subtotal * ($this->durationDiscounts[$this->duration] ?? 0);
    }

    public function getTrainingFeeProperty()
    {
        return $this->addons['training'] ? $this->addonPrices['training'] : 0;
    }

    public function getTotalPriceProperty()
    {
        if (!$this->selectedPackageData) {
            return 0;
        }

        $basePrice = $this->basePrice;
        $addonsTotal = $this->addonsTotal;

        $monthlySubtotal = $basePrice + $addonsTotal;
        $subtotal = $monthlySubtotal * $this->duration;
        $discount = $subtotal * ($this->durationDiscounts[$this->duration] ?? 0);

        if ($this->addons['training']) {
            $subtotal += $this->addonPrices['training'];
        }

        return $subtotal - $discount;
    }

    public function getCanProceedProperty()
    {
        return !empty(trim($this->projectName)) &&
               !empty(trim($this->domainName)) &&
               !empty($this->selectedPackage);
    }

    public function processOrder()
    {
        $this->validate([
            'projectName' => 'required|min:3|max:255',
            'domainName' => 'required|min:3|max:50|regex:/^[a-zA-Z0-9-]+$/',
            'selectedPackage' => 'required|exists:packages,id',
            'duration' => 'required|in:1,3,6,12',
        ], [
            'projectName.required' => 'Nama proyek wajib diisi.',
            'projectName.min' => 'Nama proyek minimal 3 karakter.',
            'domainName.required' => 'Domain wajib diisi.',
            'domainName.min' => 'Domain minimal 3 karakter.',
            'domainName.regex' => 'Domain hanya boleh berisi huruf, angka, dan tanda hubung.',
        ]);

        // Simulate processing
        $this->dispatch('processing-order');

        try {
            // Prepare special requirements
            $specialRequirements = [];
            if (!empty(trim($this->specialRequirements))) {
                $specialRequirements = ['kebutuhan_khusus' => $this->specialRequirements];
            }

            // Prepare description
            $description = "Pembuatan website {$this->projectName} dengan domain {$this->domainName}.yourdomain.com";
            $description .= " - Durasi: {$this->duration} bulan";

            // Create order in database using existing Order model structure
            $order = Order::create([
                'user_id' => Auth::id(),
                'package_id' => $this->selectedPackage,
                'description' => $description,
                'total_price' => $this->totalPrice,
                'paid_amount' => 0,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'progress_percentage' => 0,
                'special_requirements' => $specialRequirements,
                // Add additional fields for order details
                'custom_package_name' => null, // Using standard package
                'custom_features' => $this->prepareCustomFeatures(),
                'admin_notes' => null,
                'customer_notes' => null,
            ]);

            // Redirect to payment page
            return redirect()->route('payment', ['order' => $order->id]);

        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
            $this->dispatch('order-failed', [
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    protected function prepareCustomFeatures()
    {
        $features = [];

        // Add duration information
        $features[] = "Durasi layanan: {$this->duration} bulan";

        // Add selected addons
        foreach ($this->addons as $addon => $selected) {
            if ($selected) {
                $addonNames = [
                    'seo' => 'Optimasi SEO',
                    'maintenance' => 'Maintenance Bulanan',
                    'analytics' => 'Analytics Dashboard',
                    'training' => 'Training Tim'
                ];
                $features[] = $addonNames[$addon] ?? $addon;
            }
        }

        return $features;
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

            // Prepare description
            $description = "Draft: Pembuatan website {$this->projectName}";
            if (!empty($this->domainName)) {
                $description .= " dengan domain {$this->domainName}.yourdomain.com";
            }

            // Save order as draft in database
            $order = Order::create([
                'user_id' => Auth::id(),
                'package_id' => $this->selectedPackage,
                'description' => $description,
                'total_price' => $this->totalPrice,
                'paid_amount' => 0,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'progress_percentage' => 0,
                'special_requirements' => $specialRequirements,
                'custom_features' => $this->prepareCustomFeatures(),
                'admin_notes' => 'Pesanan disimpan sebagai draft oleh customer',
            ]);

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
