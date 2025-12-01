<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Package;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateOrder extends Component
{
    public $currentStep = 1;
    public $selectedPackage = null;
    public $projectName = '';
    public $domainName = '';
    public $specialRequirements = '';
    public $packages = [];
    public $isProcessing = false;
    public $errorMessage = null;
    public $successMessage = null;

    protected $listeners = ['goToStep' => 'goToStep'];

    public $redirectUrl = null;

    public function mount()
    {
        $this->loadPackages();
    }

    public function loadPackages()
    {
        $this->packages = Package::where('is_active', true)
            ->orderBy('base_price')
            ->get()
            ->toArray();
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= 3) {
            $this->currentStep = $step;
            $this->errorMessage = null;
        }
    }

    public function nextStep()
    {
        if ($this->currentStep < 3) {
            $this->currentStep++;
            $this->errorMessage = null;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->errorMessage = null;
        }
    }

    public function getSelectedPackageDataProperty()
    {
        if (!$this->selectedPackage) {
            return null;
        }

        return collect($this->packages)->firstWhere('id', $this->selectedPackage);
    }

    public function getTotalPriceProperty()
    {
        $package = $this->selectedPackageData;
        if (!$package || ($package['is_custom_price'] ?? false)) {
            return 0;
        }

        return $package['base_price'] ?? 0;
    }

    public function getCanProceedProperty()
    {
        if ($this->currentStep == 1) {
            return !empty($this->selectedPackage);
        }

        if ($this->currentStep == 2) {
            return !empty($this->selectedPackage) &&
                   !empty(trim($this->projectName)) &&
                   !empty(trim($this->domainName));
        }

        return true;
    }

    public function processOrder()
{
    $this->validate([
        'projectName' => 'required|string|max:255',
        'domainName' => 'required|string|max:100|regex:/^[a-zA-Z0-9-]+$/',
    ]);

    $this->isProcessing = true;

    try {
        // Buat request
        $request = new \Illuminate\Http\Request([
            'package_id' => $this->selectedPackage,
            'project_name' => $this->projectName,
            'domain_name' => $this->domainName,
            'special_requirements' => $this->specialRequirements,
        ]);

        // Panggil controller via HTTP (seperti form submit)
        return redirect()->route('payment.page')->with([
            'package_id' => $this->selectedPackage,
            'project_name' => $this->projectName,
            'domain_name' => $this->domainName,
            'special_requirements' => $this->specialRequirements,
        ]);

    } catch (\Exception $e) {
        $this->errorMessage = $e->getMessage();
        $this->dispatch('show-error', message: $e->getMessage());
    } finally {
        $this->isProcessing = false;
    }
}
}
