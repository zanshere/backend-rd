<div>
    <!-- Main Content -->
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-responsive safe-top">
        <div class="responsive-container max-w-7xl mx-auto">

            <!-- Header Section -->
            <div class="text-center mb-6 sm:mb-8 md:mb-12">
                <h1 class="text-responsive-xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                    Buat Pesanan Baru
                </h1>
                <p class="text-responsive-base text-gray-600 dark:text-gray-300 max-w-2xl mx-auto px-2 sm:px-4">
                    Pilih paket yang sesuai dengan kebutuhan Anda dan mulailah perjalanan digital bisnis Anda
                </p>
            </div>

            <!-- Dynamic Progress Steps - Improved for Mobile -->
            <div class="max-w-4xl mx-auto mb-6 sm:mb-8 md:mb-12 px-2 sm:px-0 overflow-x-auto scrollbar-mobile">
                <div class="flex items-center justify-between min-w-max pb-2">
                    @foreach ([1 => 'Pilih Paket', 2 => 'Detail Proyek'] as $stepNumber => $stepName)
                        <!-- Step {{ $stepNumber }} -->
                        <div class="flex items-center cursor-pointer flex-shrink-0 mx-1 sm:mx-2 touch-target"
                            wire:click="goToStep({{ $stepNumber }})">
                            <div
                                class="w-7 h-7 xs:w-8 xs:h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center
                                {{ $stepNumber < $currentStep
                                    ? 'bg-green-500'
                                    : ($stepNumber == $currentStep
                                        ? 'bg-blue-500'
                                        : 'bg-gray-300 dark:bg-gray-600') }}">
                                @if ($stepNumber < $currentStep)
                                    <i data-lucide="check" class="icon-responsive-xs text-white"></i>
                                @else
                                    <span
                                        class="text-xs xs:text-sm font-medium
                                        {{ $stepNumber == $currentStep ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $stepNumber }}
                                    </span>
                                @endif
                            </div>
                            <span
                                class="ml-1 xs:ml-2 text-xs xs:text-sm sm:text-base font-medium
                                {{ $stepNumber <= $currentStep
                                    ? ($stepNumber < $currentStep
                                        ? 'text-green-600 dark:text-green-400 font-semibold'
                                        : 'text-blue-600 dark:text-blue-400 font-semibold')
                                    : 'text-gray-500 dark:text-gray-400' }}">
                                <span class="hidden sm:inline">{{ $stepName }}</span>
                                <span class="sm:hidden xs:inline">Step {{ $stepNumber }}</span>
                                <span class="xs:hidden">{{ $stepNumber }}</span>
                            </span>
                        </div>

                        <!-- Connector (jika bukan step terakhir) -->
                        @if (!$loop->last)
                            <div
                                class="flex-1 h-1 mx-1 xs:mx-2 min-w-[15px] xs:min-w-[20px] sm:min-w-[40px]
                                {{ $stepNumber < $currentStep ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="sidebar-layout-responsive">

                <!-- Main Content -->
                <div class="sidebar-main-responsive space-y-4 sm:space-y-6 md:space-y-8">

                    <!-- Package Selection - Step 1 -->
                    @if ($currentStep == 1)
                        <div class="card-responsive">
                            <div class="flex-responsive-between mb-4 sm:mb-6">
                                <h2 class="text-responsive-md font-bold text-gray-900 dark:text-white mb-2 sm:mb-0">
                                    Pilih Paket Layanan</h2>
                                <p class="text-responsive-sm text-gray-600 dark:text-gray-400">
                                    {{ count($packages) }} paket tersedia
                                </p>
                            </div>

                            @if (empty($packages))
                                <div class="text-center py-8">
                                    <i data-lucide="package" class="icon-responsive-lg text-gray-400 mx-auto mb-4"></i>
                                    <p class="text-responsive-base text-gray-500 dark:text-gray-400">Tidak ada paket
                                        yang tersedia.</p>
                                </div>
                            @else
                                <div class="grid-responsive-2 gap-responsive">
                                    @foreach ($packages as $package)
                                        @php
                                            $packageId = $package['id'] ?? null;
                                            $packageName = $package['name'] ?? 'Unknown Package';
                                            $packageDescription = $package['description'] ?? '';
                                            $basePrice = $package['base_price'] ?? 0;
                                            $isCustomPrice = $package['is_custom_price'] ?? false;
                                            $deliveryTime = $package['delivery_time'] ?? 30;
                                            $revisionLimit = $package['revision_limit'] ?? 3;

                                            // Handle features - check if it's already an array or needs decoding
                                            $features = [];
                                            if (!empty($package['features'])) {
                                                if (is_array($package['features'])) {
                                                    $features = $package['features'];
                                                } elseif (is_string($package['features'])) {
                                                    $features = json_decode($package['features'], true) ?? [];
                                                }
                                            }

                                            $packageType = $package['type'] ?? 'usaha_kecil';
                                        @endphp

                                        <div class="cursor-pointer group touch-target"
                                            wire:click="$set('selectedPackage', {{ $packageId }})">
                                            <div
                                                class="bg-white dark:bg-gray-800 rounded-responsive border-2 {{ $selectedPackage == $packageId ? 'border-blue-500 shadow-lg ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-gray-600 group-hover:border-blue-300 dark:group-hover:border-blue-600' }} p-responsive h-full transition-all duration-300 transform {{ $selectedPackage == $packageId ? 'scale-105' : 'group-hover:scale-105' }}">
                                                <div class="text-center h-full flex flex-col">
                                                    <!-- Package Type Badge -->
                                                    @php
                                                        $typeColors = [
                                                            'usaha_kecil' =>
                                                                'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                                                            'bisnis_menengah' =>
                                                                'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200',
                                                            'bisnis' =>
                                                                'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200',
                                                            'e_commerce' =>
                                                                'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                                        ];
                                                        $typeColor =
                                                            $typeColors[$packageType] ??
                                                            'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
                                                    @endphp

                                                    <div
                                                        class="inline-flex items-center px-2 py-1 xs:px-3 xs:py-1 rounded-full text-xs font-medium {{ $typeColor }} mb-3 sm:mb-4">
                                                        {{ \App\Models\Package::getTypes()[$packageType] ?? 'Usaha Kecil' }}
                                                    </div>

                                                    <!-- Icon berdasarkan type package -->
                                                    @switch($packageType)
                                                        @case('usaha_kecil')
                                                            <div
                                                                class="w-10 h-10 xs:w-12 xs:h-12 sm:w-16 sm:h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                                                <i data-lucide="home"
                                                                    class="icon-responsive-md text-blue-600 dark:text-blue-400"></i>
                                                            </div>
                                                        @break

                                                        @case('bisnis_menengah')
                                                            <div
                                                                class="w-10 h-10 xs:w-12 xs:h-12 sm:w-16 sm:h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                                                <i data-lucide="building"
                                                                    class="icon-responsive-md text-purple-600 dark:text-purple-400"></i>
                                                            </div>
                                                        @break

                                                        @case('bisnis')
                                                            <div
                                                                class="w-10 h-10 xs:w-12 xs:h-12 sm:w-16 sm:h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                                                <i data-lucide="crown"
                                                                    class="icon-responsive-md text-orange-600 dark:text-orange-400"></i>
                                                            </div>
                                                        @break

                                                        @case('e_commerce')
                                                            <div
                                                                class="w-10 h-10 xs:w-12 xs:h-12 sm:w-16 sm:h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                                                <i data-lucide="shopping-cart"
                                                                    class="icon-responsive-md text-green-600 dark:text-green-400"></i>
                                                            </div>
                                                        @break

                                                        @default
                                                            <div
                                                                class="w-10 h-10 xs:w-12 xs:h-12 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                                                <i data-lucide="package"
                                                                    class="icon-responsive-md text-gray-600 dark:text-gray-400"></i>
                                                            </div>
                                                    @endswitch

                                                    <h3
                                                        class="text-responsive-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-1">
                                                        {{ $packageName }}
                                                    </h3>
                                                    <p
                                                        class="text-responsive-sm text-gray-600 dark:text-gray-300 mb-3 sm:mb-4 leading-relaxed line-clamp-2">
                                                        {{ $packageDescription }}
                                                    </p>

                                                    <div class="mb-3 sm:mb-4 md:mb-6">
                                                        @if ($isCustomPrice)
                                                            <div class="text-center">
                                                                <span
                                                                    class="text-responsive-base font-bold text-gray-900 dark:text-white">Harga
                                                                    Custom</span>
                                                                <p
                                                                    class="text-gray-500 dark:text-gray-400 text-responsive-sm mt-1">
                                                                    Konsultasi gratis</p>
                                                            </div>
                                                        @else
                                                            <div class="text-center">
                                                                <span
                                                                    class="text-responsive-md font-bold text-gray-900 dark:text-white">Rp
                                                                    {{ number_format($basePrice, 0, ',', '.') }}</span>
                                                                <p
                                                                    class="text-gray-500 dark:text-gray-400 text-responsive-sm mt-1">
                                                                    One-time payment</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <ul
                                                        class="text-responsive-sm text-gray-600 dark:text-gray-300 space-y-1 xs:space-y-2 sm:space-y-3 mb-3 sm:mb-4 md:mb-6 flex-grow">
                                                        @foreach ($features as $feature)
                                                            <li class="flex items-start">
                                                                <i data-lucide="check"
                                                                    class="icon-responsive-xs text-green-500 mt-0.5 mr-2 flex-shrink-0"></i>
                                                                <span
                                                                    class="leading-relaxed break-word">{{ $feature }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    <div
                                                        class="flex justify-between items-center text-responsive-sm text-gray-500 dark:text-gray-400 mb-3 sm:mb-4">
                                                        <span class="flex items-center">
                                                            <i data-lucide="clock" class="icon-responsive-xs mr-1"></i>
                                                            {{ $deliveryTime }} hari
                                                        </span>
                                                        <span class="flex items-center">
                                                            <i data-lucide="refresh-cw"
                                                                class="icon-responsive-xs mr-1"></i>
                                                            {{ $revisionLimit }} revisi
                                                        </span>
                                                    </div>

                                                    <div class="mt-auto">
                                                        <div
                                                            class="{{ $selectedPackage == $packageId ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 group-hover:bg-blue-600 group-hover:text-white' }} py-2 xs:py-3 px-4 rounded-lg font-medium transition-all duration-200 text-center text-responsive-sm touch-target">
                                                            {{ $selectedPackage == $packageId ? '✓ Dipilih' : 'Pilih Paket' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Next Button for Step 1 -->
                            @if($selectedPackage)
                                <div class="mt-6 flex justify-end">
                                    <button wire:click="nextStep"
                                            class="btn-responsive-lg bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold px-6 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center shadow-lg shadow-blue-500/25 touch-target">
                                        <span>Lanjut ke Detail Proyek</span>
                                        <i data-lucide="arrow-right" class="icon-responsive-sm ml-2"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Project Details - Step 2 -->
                    @if ($currentStep == 2 && $selectedPackage)
                        <div class="card-responsive">
                            <div class="flex-responsive-between mb-4 sm:mb-6">
                                <h2 class="text-responsive-md font-bold text-gray-900 dark:text-white mb-2 sm:mb-0">
                                    Detail Proyek</h2>
                                <button type="button" wire:click="previousStep"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 flex items-center text-responsive-sm font-medium self-start sm:self-auto touch-target">
                                    <i data-lucide="arrow-left" class="icon-responsive-xs mr-1"></i>
                                    <span class="hidden xs:inline">Kembali ke Pilih Paket</span>
                                    <span class="xs:hidden">Kembali</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 md:gap-6">
                                <div>
                                    <label
                                        class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nama Proyek <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" placeholder="Contoh: Website Toko Online Saya"
                                        wire:model="projectName"
                                        class="form-input-responsive border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" />
                                    @error('projectName')
                                        <p class="mt-2 text-responsive-sm text-red-600 flex items-center">
                                            <i data-lucide="alert-circle" class="icon-responsive-xs mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Domain yang Diinginkan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center">
                                        <input type="text" placeholder="nama-bisnis" wire:model="domainName"
                                            class="flex-1 form-input-responsive border border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" />
                                        <span
                                            class="px-3 py-2 sm:px-4 sm:py-3 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg text-gray-600 dark:text-gray-300 text-responsive-sm">
                                            .com
                                        </span>
                                    </div>
                                    <p class="mt-2 text-responsive-sm text-gray-500 dark:text-gray-400">
                                        Domain lengkap: <span
                                            class="font-medium">{{ $domainName ? $domainName . '.com' : '.com' }}</span>
                                    </p>
                                    @error('domainName')
                                        <p class="mt-2 text-responsive-sm text-red-600 flex items-center">
                                            <i data-lucide="alert-circle" class="icon-responsive-xs mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label
                                        class="block text-responsive-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Kebutuhan Khusus (Opsional)
                                    </label>
                                    <textarea placeholder="Jelaskan kebutuhan khusus proyek Anda..." wire:model="specialRequirements" rows="3"
                                        class="w-full form-input-responsive border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"></textarea>
                                    <p class="mt-2 text-responsive-sm text-gray-500 dark:text-gray-400">
                                        Informasi ini akan membantu kami memahami kebutuhan Anda dengan lebih baik.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Order Summary Sidebar -->
                <div class="sidebar-aside-responsive space-y-4 sm:space-y-6 md:space-y-8">
                    @if ($selectedPackage)
                        @php
                            $selectedPackageData = $this->selectedPackageData;
                            $packageName = $selectedPackageData['name'] ?? 'Unknown Package';
                            $packageType = $selectedPackageData['type'] ?? 'usaha_kecil';
                            $isCustomPrice = $selectedPackageData['is_custom_price'] ?? false;
                            $basePrice = $selectedPackageData['base_price'] ?? 0;
                            $totalPrice = $this->totalPrice;
                        @endphp

                        <div class="card-responsive sticky top-4 sm:top-6 md:top-8">
                            <h3
                                class="text-responsive-base font-bold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center">
                                <i data-lucide="shopping-cart" class="icon-responsive-sm mr-2"></i>
                                Ringkasan Pesanan
                            </h3>

                            <div class="space-y-2 sm:space-y-3 md:space-y-4 mb-3 sm:mb-4 md:mb-6">
                                <!-- Selected Package -->
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 min-w-0 mr-2">
                                        <span
                                            class="text-responsive-sm font-medium text-gray-600 dark:text-gray-400">Paket</span>
                                        <p
                                            class="text-responsive-base font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $packageName }}</p>
                                        <p class="text-responsive-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ \App\Models\Package::getTypes()[$packageType] ?? 'Usaha Kecil' }}
                                        </p>
                                    </div>
                                    @if (!$isCustomPrice)
                                        <span
                                            class="text-responsive-base font-semibold text-gray-900 dark:text-white flex-shrink-0">Rp
                                            {{ number_format($basePrice, 0, ',', '.') }}</span>
                                    @else
                                        <span
                                            class="text-responsive-base font-semibold text-gray-900 dark:text-white flex-shrink-0">Custom</span>
                                    @endif
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-2 sm:pt-3 md:pt-4">
                                    <div
                                        class="flex justify-between items-center text-responsive-base md:text-lg font-bold">
                                        <span class="text-gray-900 dark:text-white">Total</span>
                                        <span class="text-blue-600 dark:text-blue-400">
                                            @if ($isCustomPrice)
                                                Custom
                                            @else
                                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                @if ($currentStep == 2)
                                    <button type="button"
                                        class="w-full btn-responsive-lg bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center shadow-lg shadow-blue-500/25 touch-target"
                                        wire:click="processOrder" wire:disabled="!$this->canProceed"
                                        wire:loading.attr="disabled">
                                        <i data-lucide="lock" class="icon-responsive-sm mr-2"></i>
                                        <span wire:loading.remove>
                                            @if ($isCustomPrice)
                                                <span class="hidden xs:inline">Pesan Sekarang</span>
                                                <span class="xs:hidden">Pesan</span>
                                            @else
                                                <span class="hidden xs:inline">Lanjut ke Pembayaran</span>
                                                <span class="xs:hidden">Bayar</span>
                                            @endif
                                        </span>
                                        <span wire:loading class="flex items-center">
                                            <i data-lucide="loader" class="icon-responsive-sm mr-2 animate-spin"></i>
                                            Memproses...
                                        </span>
                                    </button>

                                    <button type="button"
                                        class="w-full btn-responsive border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center touch-target"
                                        wire:click="saveDraft" wire:loading.attr="disabled">
                                        <i data-lucide="bookmark" class="icon-responsive-sm mr-2"></i>
                                        <span wire:loading.remove>Simpan Draft</span>
                                        <span wire:loading class="flex items-center">
                                            <i data-lucide="loader" class="icon-responsive-sm mr-2 animate-spin"></i>
                                            Menyimpan...
                                        </span>
                                    </button>
                                @endif
                            </div>

                            <!-- Validation Messages -->
                            @if (!$this->canProceed && $currentStep == 2)
                                <div
                                    class="mt-3 sm:mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                    <p
                                        class="text-responsive-sm text-yellow-800 dark:text-yellow-200 flex items-center">
                                        <i data-lucide="alert-circle" class="icon-responsive-sm mr-2"></i>
                                        Lengkapi nama proyek dan domain untuk melanjutkan.
                                    </p>
                                    <!-- Debug info -->
                                    <div class="mt-2 text-xs text-yellow-700 dark:text-yellow-300">
                                        <p>Status:
                                            Project: {{ !empty(trim($projectName ?? '')) ? '✓' : '✗' }},
                                            Domain: {{ !empty(trim($domainName ?? '')) ? '✓' : '✗' }},
                                            Package: {{ !empty($selectedPackage) ? '✓' : '✗' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($isCustomPrice)
                            <div
                                class="mt-3 sm:mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <p class="text-responsive-sm text-blue-800 dark:text-blue-200 flex items-center">
                                    <i data-lucide="info" class="icon-responsive-sm mr-2"></i>
                                    Untuk paket custom, tim kami akan menghubungi Anda untuk konsultasi gratis.
                                </p>
                            </div>
                        @endif

                        <!-- Included Features -->
                        <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-600">
                            <h4
                                class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <i data-lucide="check-circle" class="icon-responsive-sm mr-2 text-green-500"></i>
                                Yang termasuk:
                            </h4>
                            <ul class="text-responsive-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <li class="flex items-center">
                                    <i data-lucide="check" class="icon-responsive-xs mr-2 text-green-500"></i>
                                    {{ $selectedPackageData['revision_limit'] ?? 3 }} revisi gratis
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="icon-responsive-xs mr-2 text-green-500"></i>
                                    Konsultasi gratis
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="icon-responsive-xs mr-2 text-green-500"></i>
                                    {{ $selectedPackageData['delivery_time'] ?? 30 }} hari pengerjaan
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="icon-responsive-xs mr-2 text-green-500"></i>
                                    Garansi 30 hari
                                </li>
                            </ul>
                        </div>
                </div>
            @else
                <!-- Placeholder ketika belum ada paket yang dipilih -->
                <div class="card-responsive sticky top-8">
                    <h3 class="text-responsive-base font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pesanan
                    </h3>
                    <div class="text-center py-6 sm:py-8">
                        <div
                            class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="package" class="icon-responsive-md text-gray-400"></i>
                        </div>
                        <p class="text-responsive-base text-gray-500 dark:text-gray-400">Pilih paket untuk melihat
                            ringkasan pesanan</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Footer Support Section -->
<footer
    class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-t border-blue-200 dark:border-blue-800 mt-8 sm:mt-12">
    <div class="responsive-container max-w-7xl mx-auto py-responsive">
        <div class="grid-responsive-3 gap-responsive">
            <div class="text-center md:text-left">
                <h4 class="font-bold text-blue-900 dark:text-blue-100 mb-2 sm:mb-3 md:mb-4 text-responsive-base">Butuh
                    Bantuan?</h4>
                <p class="text-blue-700 dark:text-blue-300 text-responsive-sm">
                    Tim support kami siap membantu Anda dengan layanan terbaik.
                </p>
            </div>

            <div class="text-center">
                <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-2 sm:mb-3 text-responsive-sm">Kontak Kami
                </h5>
                <div class="space-y-1 sm:space-y-2">
                    <a href="tel:+6285123658885"
                        class="flex items-center justify-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-responsive-sm touch-target">
                        <i data-lucide="phone" class="icon-responsive-xs mr-2"></i>
                        +62 851 2365 8885
                    </a>
                    <a href="mailto:support@example.com"
                        class="flex items-center justify-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-responsive-sm touch-target">
                        <i data-lucide="mail" class="icon-responsive-xs mr-2"></i>
                        support@ryuzen.dev
                    </a>
                </div>
            </div>

            <div class="text-center md:text-right">
                <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-2 sm:mb-3 text-responsive-sm">Layanan
                    Lainnya</h5>
                <div class="space-y-1 sm:space-y-2">
                    <a href="#"
                        class="flex items-center justify-center md:justify-end text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-responsive-sm touch-target">
                        <i data-lucide="message-circle" class="icon-responsive-xs mr-2"></i>
                        Live Chat Support
                    </a>
                    <a href="#"
                        class="flex items-center justify-center md:justify-end text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-responsive-sm touch-target">
                        <i data-lucide="help-circle" class="icon-responsive-xs mr-2"></i>
                        FAQ & Panduan
                    </a>
                </div>
            </div>
        </div>

        <div
            class="border-t border-blue-200 dark:border-blue-700 mt-4 sm:mt-6 md:mt-8 pt-4 sm:pt-6 md:pt-8 text-center">
            <p class="text-blue-600 dark:text-blue-400 text-responsive-sm">
                © 2025 Ryuzen Dev. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<!-- Success Message -->
<div id="successAlert"
    class="fixed top-4 right-4 left-4 sm:left-auto sm:right-4 z-50 max-w-full sm:max-w-sm hidden fade-in-up">
    <div
        class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 sm:p-4 shadow-lg">
        <div class="flex items-center">
            <i data-lucide="check-circle" class="icon-responsive-md text-green-500 mr-2"></i>
            <span id="successMessage"
                class="text-green-800 dark:text-green-200 text-responsive-sm font-medium flex-1 break-word"></span>
            <button onclick="this.parentElement.parentElement.classList.add('hidden')"
                class="ml-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200 touch-target">
                <i data-lucide="x" class="icon-responsive-sm"></i>
            </button>
        </div>
    </div>
</div>

    <!-- Toast Notification -->
    <div id="toast-container"
         class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"
         x-data="{
             toasts: [],
             addToast(type, message) {
                 const id = Date.now();
                 this.toasts.push({id, type, message});
                 setTimeout(() => this.removeToast(id), 5000);
             },
             removeToast(id) {
                 this.toasts = this.toasts.filter(t => t.id !== id);
             }
         }"
         x-on:show-toast.window="addToast($event.detail.type, $event.detail.message)">

        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-full"
                 :class="{
                     'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200': toast.type === 'success',
                     'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200': toast.type === 'error'
                 }"
                 class="border rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'"
                       :class="{
                           'text-green-500': toast.type === 'success',
                           'text-red-500': toast.type === 'error'
                       }"
                       class="w-5 h-5 mr-2"></i>
                    <span x-text="toast.message" class="text-sm font-medium"></span>
                    <button @click="removeToast(toast.id)"
                            :class="{
                                'text-green-600 hover:text-green-800': toast.type === 'success',
                                'text-red-600 hover:text-red-800': toast.type === 'error'
                            }"
                            class="ml-4">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
    <script>
        // Initialize Lucide icons
        document.addEventListener('alpine:initialized', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</div>
