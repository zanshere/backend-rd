<x-layouts.app :title="__('Buat Pesanan Baru')">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-8">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Buat Pesanan Baru
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Pilih paket yang sesuai dengan kebutuhan Anda dan mulailah perjalanan digital bisnis Anda
                </p>
            </div>

            <!-- Progress Steps -->
            <div class="max-w-4xl mx-auto mb-12">
                <div class="flex items-center justify-between">
                    <!-- Step 1 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                        </div>
                        <span class="ml-2 text-sm font-medium text-green-600">Pilih Paket</span>
                    </div>

                    <!-- Connector -->
                    <div class="flex-1 h-0.5 bg-green-500 mx-4"></div>

                    <!-- Step 2 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-white text-sm font-medium">2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-blue-600">Konfigurasi</span>
                    </div>

                    <!-- Connector -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>

                    <!-- Step 3 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500 text-sm font-medium">3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Pembayaran</span>
                    </div>

                    <!-- Connector -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>

                    <!-- Step 4 -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500 text-sm font-medium">4</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Selesai</span>
                    </div>
                </div>
            </div>

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Package Selection -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Pilih Paket Layanan</h2>

                        @if (empty($packages))
                            <div class="text-center py-8">
                                <i data-lucide="package" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                                <p class="text-gray-500">Tidak ada paket yang tersedia.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach ($packages as $package)
                                    <div class="cursor-pointer"
                                        wire:click="$set('selectedPackage', {{ $package['id'] }})">
                                        <div
                                            class="bg-white rounded-lg border-2 {{ $selectedPackage == $package['id'] ? 'border-blue-500 shadow-lg' : 'border-gray-200 hover:shadow-lg' }} p-6 h-full transition-all duration-300">
                                            <div class="text-center h-full flex flex-col">
                                                <!-- Icon berdasarkan type package -->
                                                @switch($package['type'])
                                                    @case('usaha_kecil')
                                                        <i data-lucide="home" class="w-12 h-12 text-green-500 mx-auto mb-4"></i>
                                                    @break

                                                    @case('bisnis_menengah')
                                                        <i data-lucide="building"
                                                            class="w-12 h-12 text-blue-500 mx-auto mb-4"></i>
                                                    @break

                                                    @case('bisnis')
                                                        <i data-lucide="briefcase"
                                                            class="w-12 h-12 text-purple-500 mx-auto mb-4"></i>
                                                    @break

                                                    @case('e_commerce')
                                                        <i data-lucide="shopping-cart"
                                                            class="w-12 h-12 text-orange-500 mx-auto mb-4"></i>
                                                    @break

                                                    @default
                                                        <i data-lucide="package"
                                                            class="w-12 h-12 text-gray-500 mx-auto mb-4"></i>
                                                @endswitch

                                                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                                    {{ $package['name'] }}</h3>
                                                <p class="text-sm text-gray-600 mb-4">{{ $package['description'] }}</p>

                                                <div class="mb-4">
                                                    @if ($package['is_custom_price'])
                                                        <span class="text-2xl font-bold text-gray-900">Harga
                                                            Custom</span>
                                                        <p class="text-gray-500 text-sm">Konsultasi gratis</p>
                                                    @else
                                                        <span class="text-3xl font-bold text-gray-900">Rp
                                                            {{ number_format($package['base_price'], 0, ',', '.') }}</span>
                                                        <p class="text-gray-500 text-sm">One-time payment</p>
                                                    @endif
                                                </div>

                                                <ul class="text-sm text-gray-600 space-y-2 mb-6 flex-grow">
                                                    @php
                                                        // Handle features - bisa berupa array atau JSON string
                                                        $features = is_array($package['features'])
                                                            ? $package['features']
                                                            : json_decode($package['features'], true) ?? [];
                                                    @endphp
                                                    @foreach (array_slice($features, 0, 4) as $feature)
                                                        <li class="flex items-center">
                                                            <i data-lucide="check"
                                                                class="w-4 h-4 text-green-500 mr-2"></i>
                                                            {{ $feature }}
                                                        </li>
                                                    @endforeach
                                                    @if (count($features) > 4)
                                                        <li class="text-blue-600 text-sm">
                                                            +{{ count($features) - 4 }} fitur lainnya...
                                                        </li>
                                                    @endif
                                                </ul>

                                                <div
                                                    class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                                    <span class="flex items-center">
                                                        <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                                        {{ $package['delivery_time'] }} hari
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>
                                                        {{ $package['revision_limit'] }} revisi
                                                    </span>
                                                </div>

                                                <div class="mt-auto">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $selectedPackage == $package['id'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} w-full justify-center">
                                                        {{ $selectedPackage == $package['id'] ? 'Dipilih' : 'Pilih Paket' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if ($selectedPackage)
                        <!-- Project Details -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Proyek</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Proyek <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" placeholder="Masukkan nama proyek Anda"
                                        wire:model="projectName"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" />
                                    @error('projectName')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Domain yang Diinginkan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" placeholder="contoh: bisnis-saya" wire:model="domainName"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Domain akan menjadi:
                                        {{ $domainName ? $domainName . '.yourdomain.com' : 'yourdomain.com' }}
                                    </p>
                                    @error('domainName')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Durasi Layanan</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @php
                                            $durationOptions = [
                                                1 => '1 Bulan',
                                                3 => '3 Bulan (Diskon 5%)',
                                                6 => '6 Bulan (Diskon 10%)',
                                                12 => '1 Tahun (Diskon 15%)'
                                            ];
                                        @endphp
                                        @foreach ($durationOptions as $value => $label)
                                            <label class="relative flex cursor-pointer">
                                                <input type="radio" value="{{ $value }}" wire:model="duration"
                                                    class="sr-only">
                                                <div
                                                    class="flex items-center justify-center w-full px-4 py-3 border-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $duration == $value ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700 hover:border-gray-300' }}">
                                                    {{ $label }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Kebutuhan Khusus
                                    </label>
                                    <textarea placeholder="Jelaskan kebutuhan khusus proyek Anda..." wire:model="specialRequirements" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Services -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Layanan Tambahan</h2>
                            <div class="space-y-4">
                                @php
                                    $addonOptions = [
                                        'seo' => 'Optimasi SEO (+Rp 150rb/bulan)',
                                        'maintenance' => 'Maintenance Bulanan (+Rp 200rb/bulan)',
                                        'analytics' => 'Analytics Dashboard (+Rp 100rb/bulan)',
                                        'training' => 'Training Tim (+Rp 500rb/sesi)',
                                    ];
                                @endphp
                                @foreach ($addonOptions as $key => $label)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" wire:model="addons.{{ $key }}"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 transition-colors duration-200">
                                        <span
                                            class="ml-3 text-sm text-gray-700 group-hover:text-gray-900 transition-colors duration-200">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Order Summary Sidebar -->
                <div class="space-y-8">
                    @if ($selectedPackage)
                        @php
                            // Mendapatkan data paket yang dipilih
                            $selectedPackageData = collect($packages)->firstWhere('id', $selectedPackage);

                            // Menghitung total harga
                            $basePrice = $selectedPackageData['is_custom_price'] ? 0 : $selectedPackageData['base_price'];

                            // Menghitung diskon berdasarkan durasi
                            $durationDiscounts = [
                                1 => 0,
                                3 => 0.05,
                                6 => 0.10,
                                12 => 0.15
                            ];

                            $discountRate = $durationDiscounts[$duration] ?? 0;
                            $discountAmount = $basePrice * $discountRate;

                            // Menghitung total addons bulanan
                            $addonsMonthly = 0;
                            if ($addons['seo'] ?? false) $addonsMonthly += 150000;
                            if ($addons['maintenance'] ?? false) $addonsMonthly += 200000;
                            if ($addons['analytics'] ?? false) $addonsMonthly += 100000;

                            // Menghitung total addons
                            $addonsTotal = $addonsMonthly * $duration;

                            // Menambahkan biaya training jika dipilih
                            $trainingFee = ($addons['training'] ?? false) ? 500000 : 0;

                            // Total harga
                            $totalPrice = ($basePrice - $discountAmount) + $addonsTotal + $trainingFee;

                            // Cek apakah bisa melanjutkan
                            $canProceed = !empty($projectName) && !empty($domainName);
                        @endphp

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h3>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Paket:</span>
                                    <span class="font-medium text-gray-900">{{ $selectedPackageData['name'] }}</span>
                                </div>

                                @if (!$selectedPackageData['is_custom_price'])
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Harga Paket:</span>
                                        <span class="font-medium text-gray-900">Rp
                                            {{ number_format($basePrice, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Harga Paket:</span>
                                        <span class="font-medium text-gray-900">Custom</span>
                                    </div>
                                @endif

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Layanan Tambahan:</span>
                                    <span class="font-medium text-gray-900">Rp
                                        {{ number_format($addonsTotal, 0, ',', '.') }}/bln</span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Durasi:</span>
                                    <span class="font-medium text-gray-900">{{ $duration }} Bulan</span>
                                </div>

                                @if ($addons['seo'] ?? false)
                                    <div class="flex justify-between text-sm text-green-600">
                                        <span class="flex items-center">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                            Optimasi SEO
                                        </span>
                                        <span>Rp 150rb/bln</span>
                                    </div>
                                @endif

                                @if ($addons['maintenance'] ?? false)
                                    <div class="flex justify-between text-sm text-green-600">
                                        <span class="flex items-center">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                            Maintenance
                                        </span>
                                        <span>Rp 200rb/bln</span>
                                    </div>
                                @endif

                                @if ($addons['analytics'] ?? false)
                                    <div class="flex justify-between text-sm text-green-600">
                                        <span class="flex items-center">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                            Analytics
                                        </span>
                                        <span>Rp 100rb/bln</span>
                                    </div>
                                @endif

                                @if ($addons['training'] ?? false)
                                    <div class="flex justify-between text-sm text-green-600">
                                        <span class="flex items-center">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                            Training Tim
                                        </span>
                                        <span>Rp 500rb</span>
                                    </div>
                                @endif

                                @if ($discountAmount > 0)
                                    <div class="flex justify-between text-sm text-red-600">
                                        <span>Diskon {{ $discountRate * 100 }}%:</span>
                                        <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @if ($trainingFee > 0)
                                    <div class="flex justify-between text-sm">
                                        <span>Biaya Training:</span>
                                        <span>Rp {{ number_format($trainingFee, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <div class="border-t pt-3 mt-3">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-900">Total:</span>
                                        <span class="text-blue-600">
                                            @if ($selectedPackageData['is_custom_price'])
                                                Custom
                                            @else
                                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                    @if (!$selectedPackageData['is_custom_price'])
                                        <div class="text-xs text-gray-500 text-right mt-1">
                                            {{ $duration }} bulan
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button type="button"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center"
                                wire:click="processOrder"
                                :disabled="!$canProceed || $selectedPackageData['is_custom_price']"
                                wire:loading.attr="disabled">
                                <i data-lucide="lock" class="w-4 h-4 mr-2"></i>
                                <span wire:loading.remove>
                                    @if ($selectedPackageData['is_custom_price'])
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
                                class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 mt-3 flex items-center justify-center"
                                wire:click="saveDraft" wire:loading.attr="disabled">
                                <i data-lucide="bookmark" class="w-4 h-4 mr-2"></i>
                                <span wire:loading.remove>Simpan Draft</span>
                                <span wire:loading class="flex items-center">
                                    <i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>
                                    Menyimpan...
                                </span>
                            </button>

                            @if (!$canProceed && !$selectedPackageData['is_custom_price'])
                                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <p class="text-sm text-yellow-800 flex items-center">
                                        <i data-lucide="alert-circle" class="w-4 h-4 mr-2"></i>
                                        Lengkapi semua informasi yang diperlukan untuk melanjutkan.
                                    </p>
                                </div>
                            @endif

                            @if ($selectedPackageData['is_custom_price'])
                                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-sm text-blue-800 flex items-center">
                                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                                        Untuk paket custom, tim kami akan menghubungi Anda untuk konsultasi gratis.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Placeholder ketika belum ada paket yang dipilih -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                            <div class="text-center py-8">
                                <i data-lucide="package" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                                <p class="text-gray-500">Pilih paket untuk melihat ringkasan pesanan</p>
                            </div>
                        </div>
                    @endif

                    <!-- Support Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <div class="flex items-start space-x-3">
                            <i data-lucide="phone" class="w-6 h-6 text-blue-600 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-2">Butuh Bantuan?</h4>
                                <p class="text-sm text-blue-700 mb-3">
                                    Tim support kami siap membantu Anda 24/7
                                </p>
                                <div class="space-y-1 text-sm text-blue-600">
                                    <div class="flex items-center">
                                        <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i>
                                        <span>Live Chat</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                        <span>+62 21 1234 5678</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                        <span>support@example.com</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div id="successAlert" class="fixed top-4 right-4 z-50 max-w-sm hidden">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2"></i>
                <span id="successMessage" class="text-green-800 text-sm font-medium"></span>
                <button onclick="this.parentElement.parentElement.classList.add('hidden')"
                    class="ml-auto text-green-600 hover:text-green-800">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function() {
            // Initialize Lucide icons
            if (window.Lucide) {
                window.LucideIcons = window.Lucide.createIcons();
            }

            Livewire.on('order-processed', (event) => {
                const alert = document.getElementById('successAlert');
                const message = document.getElementById('successMessage');
                message.textContent = event.message;
                alert.classList.remove('hidden');
                setTimeout(() => {
                    alert.classList.add('hidden');
                }, 5000);
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
</x-layouts.app>
