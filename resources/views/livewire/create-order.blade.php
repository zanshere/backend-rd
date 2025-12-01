<div>
    <!-- Main Content -->
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-responsive safe-top">
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

            <!-- Progress Steps -->
            <div class="max-w-4xl mx-auto mb-6 sm:mb-8 md:mb-12 px-2 sm:px-0">
                <div class="flex items-center justify-between">
                    @foreach([1 => 'Pilih Paket', 2 => 'Detail Proyek', 3 => 'Konfirmasi'] as $stepNumber => $stepName)
                        <div class="flex items-center cursor-pointer" wire:click="goToStep({{ $stepNumber }})">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center
                                {{ $stepNumber < $currentStep ? 'bg-green-500' :
                                   ($stepNumber == $currentStep ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }}">
                                @if($stepNumber < $currentStep)
                                    <i data-lucide="check" class="w-5 h-5 text-white"></i>
                                @else
                                    <span class="text-sm font-medium {{ $stepNumber == $currentStep ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $stepNumber }}
                                    </span>
                                @endif
                            </div>
                            <span class="ml-2 text-sm font-medium
                                {{ $stepNumber <= $currentStep ?
                                   ($stepNumber < $currentStep ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400') :
                                   'text-gray-500 dark:text-gray-400' }}">
                                {{ $stepName }}
                            </span>
                        </div>

                        @if(!$loop->last)
                            <div class="flex-1 h-1 mx-4
                                {{ $stepNumber < $currentStep ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Error Message -->
            @if($errorMessage)
            <div class="mb-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-2"></i>
                        <span class="text-red-700 dark:text-red-300">{{ $errorMessage }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Success Message -->
            @if($successMessage)
            <div class="mb-6">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2"></i>
                        <span class="text-green-700 dark:text-green-300">{{ $successMessage }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">

                    @if($currentStep == 1)
                    <!-- Step 1: Package Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Pilih Paket Layanan</h2>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ count($packages) }} paket tersedia
                            </span>
                        </div>

                        @if(empty($packages))
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="package" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada paket yang tersedia.</p>
                        </div>
                        @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($packages as $package)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:border-blue-500 dark:hover:border-blue-400 cursor-pointer transition-colors
                                {{ $selectedPackage == $package['id'] ? 'ring-2 ring-blue-500 border-blue-500' : '' }}"
                                wire:click="$set('selectedPackage', {{ $package['id'] }})">

                                <!-- Package Type Badge -->
                                @php
                                    $typeColors = [
                                        'usaha_kecil' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'bisnis_menengah' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        'bisnis' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                        'e_commerce' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    ];
                                    $typeColor = $typeColors[$package['type'] ?? 'usaha_kecil'] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                @endphp

                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mb-4 {{ $typeColor }}">
                                    {{ $package['type_display'] ?? 'Usaha Kecil' }}
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $package['name'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ $package['description'] }}</p>

                                <div class="mb-4">
                                    @if($package['is_custom_price'])
                                        <div class="text-center">
                                            <span class="text-xl font-bold text-gray-900 dark:text-white">Harga Custom</span>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Konsultasi gratis</p>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <span class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($package['base_price'], 0, ',', '.') }}</span>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">One-time payment</p>
                                        </div>
                                    @endif
                                </div>

                                <ul class="space-y-2 mb-6">
                                    @foreach(($package['features_array'] ?? []) as $feature)
                                    <li class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                        <i data-lucide="check" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                        <span>{{ $feature }}</span>
                                    </li>
                                    @endforeach
                                </ul>

                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                        {{ $package['delivery_time'] ?? 30 }} hari
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>
                                        {{ $package['revision_limit'] ?? 3 }} revisi
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Next Button -->
                        @if($selectedPackage)
                        <div class="mt-8 flex justify-end">
                            <button wire:click="nextStep"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center">
                                <span>Lanjut ke Detail Proyek</span>
                                <i data-lucide="arrow-right" class="w-5 h-5 ml-2"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($currentStep == 2 && $selectedPackage)
                    <!-- Step 2: Project Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detail Proyek</h2>
                            <button type="button" wire:click="previousStep"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 flex items-center text-sm font-medium">
                                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                                Kembali
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nama Proyek <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="projectName"
                                       placeholder="Contoh: Website Toko Online Saya"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('projectName')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Domain yang Diinginkan <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <input type="text"
                                           wire:model="domainName"
                                           placeholder="nama-bisnis"
                                           class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <span class="px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg text-gray-600 dark:text-gray-300">
                                        .com
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Domain lengkap: <span class="font-medium">{{ $domainName ? $domainName . '.com' : '.com' }}</span>
                                </p>
                                @error('domainName')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Kebutuhan Khusus (Opsional)
                                </label>
                                <textarea wire:model="specialRequirements"
                                          rows="4"
                                          placeholder="Jelaskan kebutuhan khusus proyek Anda..."
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Informasi ini akan membantu kami memahami kebutuhan Anda dengan lebih baik.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($currentStep == 3 && $selectedPackage)
                    <!-- Step 3: Confirmation -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Konfirmasi Pesanan</h2>

                        <div class="space-y-6">
                            <!-- Order Summary -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pesanan</h3>

                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Paket</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $selectedPackageData['name'] ?? '' }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Nama Proyek</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $projectName }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Domain</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $domainName }}.com</span>
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                        <div class="flex justify-between text-lg font-bold">
                                            <span class="text-gray-900 dark:text-white">Total</span>
                                            <span class="text-blue-600 dark:text-blue-400">
                                                @if($selectedPackageData['is_custom_price'] ?? false)
                                                    Custom
                                                @else
                                                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            Dengan melanjutkan, Anda menyetujui syarat dan ketentuan kami.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Order Summary Sidebar -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pesanan</h3>

                        @if($selectedPackage)
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Paket</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $selectedPackageData['name'] ?? '' }}</span>
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-gray-900 dark:text-white">Total</span>
                                        <span class="text-blue-600 dark:text-blue-400">
                                            @if($selectedPackageData['is_custom_price'] ?? false)
                                                Custom
                                            @else
                                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 space-y-3">
                                @if($currentStep == 2)
                                    <button wire:click="processOrder"
                                            wire:loading.attr="disabled"
                                            wire:target="processOrder"
                                            class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center">
                                        <i data-lucide="lock" class="w-5 h-5 mr-2"></i>
                                        <span wire:loading.remove wire:target="processOrder">
                                            @if($selectedPackageData['is_custom_price'] ?? false)
                                                Pesan Sekarang
                                            @else
                                                Lanjut ke Pembayaran
                                            @endif
                                        </span>
                                        <span wire:loading wire:target="processOrder" class="flex items-center">
                                            <i data-lucide="loader" class="w-5 h-5 mr-2 animate-spin"></i>
                                            Memproses...
                                        </span>
                                    </button>

                                    <button wire:click="saveDraft"
                                            wire:loading.attr="disabled"
                                            wire:target="saveDraft"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                                        <i data-lucide="bookmark" class="w-5 h-5 mr-2"></i>
                                        <span wire:loading.remove wire:target="saveDraft">Simpan Draft</span>
                                        <span wire:loading wire:target="saveDraft" class="flex items-center">
                                            <i data-lucide="loader" class="w-5 h-5 mr-2 animate-spin"></i>
                                            Menyimpan...
                                        </span>
                                    </button>
                                @endif
                            </div>

                            <!-- Validation Message -->
                            @if(!$canProceed && $currentStep == 2)
                                <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                        Lengkapi semua informasi yang diperlukan untuk melanjutkan.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="package" class="w-6 h-6 text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Pilih paket untuk melihat ringkasan</p>
                            </div>
                        @endif
                    </div>

                    <!-- Support Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                        <div class="flex items-start">
                            <i data-lucide="help-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5"></i>
                            <div>
                                <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-1">Butuh Bantuan?</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">
                                    Tim support kami siap membantu Anda 24/7.
                                </p>
                                <div class="space-y-1">
                                    <a href="https://wa.me/6285123658885" target="_blank"
                                       class="flex items-center text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                                        <i data-lucide="message-circle" class="w-4 h-4 mr-1"></i>
                                        WhatsApp Support
                                    </a>
                                    <a href="mailto:support@ryuzen.dev"
                                       class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                                        <i data-lucide="mail" class="w-4 h-4 mr-1"></i>
                                        support@ryuzen.dev
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function() {
            // Initialize Lucide icons
            if (window.Lucide) {
                Lucide.createIcons();
            }

            // Handle order created event
            Livewire.on('order-created', (event) => {
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'fixed top-4 right-4 z-50 max-w-sm fade-in-up';
                alert.innerHTML = `
                    <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            <span>${event.message}</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(alert);

                if (window.Lucide) {
                    Lucide.createIcons();
                }

                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 3000);
            });

            // Handle draft saved event
            Livewire.on('draft-saved', (event) => {
                const alert = document.createElement('div');
                alert.className = 'fixed top-4 right-4 z-50 max-w-sm fade-in-up';
                alert.innerHTML = `
                    <div class="bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            <span>${event.message}</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(alert);

                if (window.Lucide) {
                    Lucide.createIcons();
                }

                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 3000);
            });

            // Handle redirect
            Livewire.on('redirect-delayed', (event) => {
                setTimeout(() => {
                    window.location.href = event.url;
                }, event.delay);
            });

            // Handle errors
            Livewire.on('order-failed', (event) => {
                const alert = document.createElement('div');
                alert.className = 'fixed top-4 right-4 z-50 max-w-sm fade-in-up';
                alert.innerHTML = `
                    <div class="bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                            <span>${event.message}</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(alert);

                if (window.Lucide) {
                    Lucide.createIcons();
                }

                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            });
        });

        // Re-initialize icons on Livewire updates
        document.addEventListener('livewire:update', function() {
            if (window.Lucide) {
                Lucide.createIcons();
            }
        });
    </script>
</div>
