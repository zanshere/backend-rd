<div>
    <!-- Main Content -->
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-8">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Buat Pesanan Baru
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Pilih paket yang sesuai dengan kebutuhan Anda dan mulailah perjalanan digital bisnis Anda
                </p>
            </div>

            <!-- Dynamic Progress Steps -->
            <div class="max-w-4xl mx-auto mb-12">
                <div class="flex items-center justify-between">
                    @foreach($steps as $stepNumber => $stepName)
                        <!-- Step {{ $stepNumber }} -->
                        <div class="flex items-center cursor-pointer" wire:click="goToStep({{ $stepNumber }})">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                {{ $stepNumber < $currentStep ? 'bg-green-500' :
                                   ($stepNumber == $currentStep ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }}">
                                @if($stepNumber < $currentStep)
                                    <i data-lucide="check" class="w-5 h-5 text-white"></i>
                                @else
                                    <span class="text-sm font-medium
                                        {{ $stepNumber == $currentStep ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $stepNumber }}
                                    </span>
                                @endif
                            </div>
                            <span class="ml-3 text-base font-medium
                                {{ $stepNumber <= $currentStep ?
                                    ($stepNumber < $currentStep ? 'text-green-600 dark:text-green-400 font-semibold' :
                                     'text-blue-600 dark:text-blue-400 font-semibold') :
                                    'text-gray-500 dark:text-gray-400' }}">
                                {{ $stepName }}
                            </span>
                        </div>

                        <!-- Connector (jika bukan step terakhir) -->
                        @if(!$loop->last)
                            <div class="flex-1 h-1 mx-4
                                {{ $stepNumber < $currentStep ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Package Selection - Step 1 -->
                    @if($currentStep == 1)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pilih Paket Layanan</h2>
                        </div>

                        @if (empty($packages))
                            <div class="text-center py-8">
                                <i data-lucide="package" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada paket yang tersedia.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach ($packages as $package)
                                    @php
                                        // Safe access dengan null coalescing untuk menghindari undefined array key
                                        $packageId = $package['id'] ?? null;
                                        $isPopular = $package['is_popular'] ?? false;
                                        $packageType = $package['type'] ?? 'default';
                                        $packageName = $package['name'] ?? 'Unknown Package';
                                        $packageDescription = $package['description'] ?? '';
                                        $isCustomPrice = $package['is_custom_price'] ?? false;
                                        $basePrice = $package['base_price'] ?? 0;
                                        $deliveryTime = $package['delivery_time'] ?? 0;

                                        // Handle features array safely
                                        $features = [];
                                        if (isset($package['features'])) {
                                            $features = is_array($package['features'])
                                                ? $package['features']
                                                : json_decode($package['features'], true) ?? [];
                                        }
                                    @endphp

                                    <div class="cursor-pointer group" wire:click="$set('selectedPackage', {{ $packageId }})">
                                        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 {{ $selectedPackage == $packageId ? 'border-blue-500 shadow-lg ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-gray-600 group-hover:border-blue-300 dark:group-hover:border-blue-600' }} p-6 h-full transition-all duration-300 transform {{ $selectedPackage == $packageId ? 'scale-105' : 'group-hover:scale-105' }}">
                                            <div class="text-center h-full flex flex-col">
                                                <!-- Badge untuk paket populer - Fixed dengan safe access -->
                                                @if($isPopular)
                                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 mb-4">
                                                        <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                                                        Paling Populer
                                                    </div>
                                                @endif

                                                <!-- Icon berdasarkan type package - Fixed dengan safe access -->
                                                @switch($packageType)
                                                    @case('basic')
                                                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                                            <i data-lucide="home" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                                                        </div>
                                                    @break

                                                    @case('business')
                                                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                                            <i data-lucide="building" class="w-8 h-8 text-purple-600 dark:text-purple-400"></i>
                                                        </div>
                                                    @break

                                                    @case('premium')
                                                        <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                                            <i data-lucide="crown" class="w-8 h-8 text-orange-600 dark:text-orange-400"></i>
                                                        </div>
                                                    @break

                                                    @case('ecommerce')
                                                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                                            <i data-lucide="shopping-cart" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                                                        </div>
                                                    @break

                                                    @default
                                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                                            <i data-lucide="package" class="w-8 h-8 text-gray-600 dark:text-gray-400"></i>
                                                        </div>
                                                @endswitch

                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                                    {{ $packageName }}
                                                </h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                                                    {{ $packageDescription }}
                                                </p>

                                                <div class="mb-6">
                                                    @if ($isCustomPrice)
                                                        <div class="text-center">
                                                            <span class="text-2xl font-bold text-gray-900 dark:text-white">Harga Custom</span>
                                                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Konsultasi gratis untuk menentukan kebutuhan</p>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <span class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
                                                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">One-time payment • Tidak ada biaya tersembunyi</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-3 mb-6 flex-grow">
                                                    @foreach ($features as $feature)
                                                        <li class="flex items-start">
                                                            <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                                            <span class="leading-relaxed">{{ $feature }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                                                    <span class="flex items-center">
                                                        <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                                        {{ $deliveryTime }} hari pengerjaan
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>
                                                        Revisi Unlimited
                                                    </span>
                                                </div>

                                                <div class="mt-auto">
                                                    <div class="{{ $selectedPackage == $packageId ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 group-hover:bg-blue-600 group-hover:text-white' }} py-3 px-4 rounded-lg font-medium transition-all duration-200 text-center">
                                                        {{ $selectedPackage == $packageId ? '✓ Dipilih' : 'Pilih Paket' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Project Details - Step 2 -->
                    @if ($currentStep == 2 && $selectedPackage)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Proyek</h2>
                                <button type="button" wire:click="goToStep(1)"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 flex items-center text-sm font-medium">
                                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                                    Kembali ke Pilih Paket
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nama Proyek <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" placeholder="Contoh: Website Toko Online Saya" wire:model="projectName"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" />
                                    @error('projectName')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Domain yang Diinginkan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center">
                                        <input type="text" placeholder="nama-bisnis" wire:model="domainName"
                                            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" />
                                        <span class="px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg text-gray-600 dark:text-gray-300 text-sm">
                                            .com
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Domain lengkap: <span class="font-medium">{{ $domainName ? $domainName . '.com' : '.com' }}</span>
                                    </p>
                                    @error('domainName')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Kebutuhan Khusus (Opsional)
                                    </label>
                                    <textarea placeholder="Jelaskan kebutuhan khusus proyek Anda, seperti fitur tambahan, preferensi desain, atau kebutuhan bisnis spesifik..."
                                        wire:model="specialRequirements" rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"></textarea>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Informasi ini akan membantu kami memahami kebutuhan Anda dengan lebih baik.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Order Summary Sidebar -->
                <div class="space-y-8">
                    @if ($selectedPackage)
                        @php
                            $selectedPackageData = collect($packages)->firstWhere('id', $selectedPackage);
                            $basePrice = ($selectedPackageData['is_custom_price'] ?? false) ? 0 : ($selectedPackageData['base_price'] ?? 0);
                            $totalPrice = $basePrice;
                            $canProceed = !empty($projectName) && !empty($domainName);
                            $isCustomPrice = $selectedPackageData['is_custom_price'] ?? false;
                            $packageName = $selectedPackageData['name'] ?? 'Unknown Package';
                        @endphp

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i data-lucide="shopping-cart" class="w-5 h-5 mr-2"></i>
                                Ringkasan Pesanan
                            </h3>

                            <div class="space-y-4 mb-6">
                                <!-- Selected Package -->
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Paket</span>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $packageName }}</p>
                                    </div>
                                    @if (!$isCustomPrice)
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Custom</span>
                                    @endif
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <div class="flex justify-between items-center text-lg font-bold">
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
                                @if($currentStep == 2)
                                <button type="button"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center shadow-lg shadow-blue-500/25"
                                    wire:click="processOrder"
                                    :disabled="!$canProceed || $isCustomPrice"
                                    wire:loading.attr="disabled">
                                    <i data-lucide="lock" class="w-4 h-4 mr-2"></i>
                                    <span wire:loading.remove>
                                        @if ($isCustomPrice)
                                            Konsultasi Gratis
                                        @else
                                            Lanjut ke Pembayaran
                                        @endif
                                    </span>
                                    <span wire:loading class="flex items-center">
                                        <i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>
                                        Memproses...
                                    </span>
                                </button>

                                <button type="button"
                                    class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 py-3 px-4 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center"
                                    wire:click="saveDraft" wire:loading.attr="disabled">
                                    <i data-lucide="bookmark" class="w-4 h-4 mr-2"></i>
                                    <span wire:loading.remove>Simpan Draft</span>
                                    <span wire:loading class="flex items-center">
                                        <i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>
                                        Menyimpan...
                                    </span>
                                </button>
                                @endif
                            </div>

                            <!-- Validation Messages -->
                            @if (!$canProceed && !$isCustomPrice && $currentStep == 2)
                                <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200 flex items-center">
                                        <i data-lucide="alert-circle" class="w-4 h-4 mr-2"></i>
                                        Lengkapi nama proyek dan domain untuk melanjutkan.
                                    </p>
                                </div>
                            @endif

                            @if ($isCustomPrice)
                                <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                    <p class="text-sm text-blue-800 dark:text-blue-200 flex items-center">
                                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                                        Untuk paket custom, tim kami akan menghubungi Anda untuk konsultasi gratis dalam 24 jam.
                                    </p>
                                </div>
                            @endif

                            <!-- Included Features -->
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-500"></i>
                                    Yang termasuk:
                                </h4>
                                <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-2">
                                    <li class="flex items-center">
                                        <i data-lucide="check" class="w-3 h-3 mr-2 text-green-500"></i>
                                        Revisi Unlimited gratis
                                    </li>
                                    <li class="flex items-center">
                                        <i data-lucide="check" class="w-3 h-3 mr-2 text-green-500"></i>
                                        Konsultasi gratis selama pengerjaan
                                    </li>
                                    <li class="flex items-center">
                                        <i data-lucide="check" class="w-3 h-3 mr-2 text-green-500"></i>
                                        Optimasi SEO dasar
                                    </li>
                                    <li class="flex items-center">
                                        <i data-lucide="check" class="w-3 h-3 mr-2 text-green-500"></i>
                                        Garansi 30 hari setelah selesai
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <!-- Placeholder ketika belum ada paket yang dipilih -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pesanan</h3>
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="package" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Pilih paket untuk melihat ringkasan pesanan</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Support Section -->
    <footer class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-t border-blue-200 dark:border-blue-800 mt-16">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center md:text-left">
                    <h4 class="font-bold text-blue-900 dark:text-blue-100 mb-4 text-lg">Butuh Bantuan?</h4>
                    <p class="text-blue-700 dark:text-blue-300 text-sm">
                        Tim support kami siap membantu Anda 24/7 dengan layanan terbaik.
                    </p>
                </div>

                <div class="text-center">
                    <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-3">Kontak Kami</h5>
                    <div class="space-y-2">
                        <a href="tel:+6285123658885" class="flex items-center justify-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-sm">
                            <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                            +62 851 2365 8885
                        </a>
                        <a href="mailto:support@example.com" class="flex items-center justify-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-sm">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            support@ryuzen.dev
                        </a>
                    </div>
                </div>

                <div class="text-center md:text-right">
                    <h5 class="font-semibold text-blue-900 dark:text-blue-100 mb-3">Layanan Lainnya</h5>
                    <div class="space-y-2">
                        <a href="#" class="flex items-center justify-center md:justify-end text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-sm">
                            <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i>
                            Live Chat Support
                        </a>
                        <a href="#" class="flex items-center justify-center md:justify-end text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors text-sm">
                            <i data-lucide="help-circle" class="w-4 h-4 mr-2"></i>
                            FAQ & Panduan
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-blue-200 dark:border-blue-700 mt-8 pt-8 text-center">
                <p class="text-blue-600 dark:text-blue-400 text-sm">
                    © 2025 Ryuzen Dev. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Success Message -->
    <div id="successAlert" class="fixed top-4 right-4 z-50 max-w-sm hidden">
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-lg">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2"></i>
                <span id="successMessage" class="text-green-800 dark:text-green-200 text-sm font-medium"></span>
                <button onclick="this.parentElement.parentElement.classList.add('hidden')"
                    class="ml-auto text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function() {
            // Initialize Lucide icons
            if (window.Lucide) {
                window.Lucide.createIcons();
            }

            Livewire.on('order-processed', (event) => {
                const alert = document.getElementById('successAlert');
                const message = document.getElementById('successMessage');
                message.textContent = event.message;
                alert.classList.remove('hidden');
                setTimeout(() => {
                    alert.classList.add('hidden');
                }, 5000);

                // Redirect ke halaman payment setelah 2 detik
                setTimeout(() => {
                    window.location.href = '/payment';
                }, 2000);
            });

            Livewire.on('draft-saved', (event) => {
                const alert = document.getElementById('successAlert');
                const message = document.getElementById('successMessage');
                message.textContent = event.message;
                alert.classList.remove('hidden');
                setTimeout(() => {
                    alert.classList.add('hidden');
                }, 3000);
            });
        });

        // Re-initialize icons after Livewire updates
        document.addEventListener('livewire:update', function() {
            if (window.LucideIcons) {
                window.LucideIcons.replace();
            }
        });
    </script>
</div>
