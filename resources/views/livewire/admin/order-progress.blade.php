<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Progress Pesanan</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola progress pesanan #{{ $order->order_number }}</p>
        </div>

        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <!-- Back Button -->
            <a
                href="{{ route('admin.orders') }}"
                class="flex items-center gap-2 px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-300 dark:border-zinc-600 rounded-lg"
            >
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>

            <!-- Status Badge -->
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                @elseif($order->status === 'progress') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400
                @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                @elseif($order->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                @endif">
                {{ $this->displayStatus }}
            </span>
        </div>
    </div>

    <!-- Order Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Progress -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Progress Overview -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Progress Overview</h3>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                        <span>Progress Pengerjaan</span>
                        <span>{{ $order->progress_percentage ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                        <div
                            class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                            style="width: {{ $order->progress_percentage ?? 0 }}%"
                        ></div>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="space-y-4">
                    @foreach($this->progressSteps as $step)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                @if($step['completed']) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($step['current']) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400
                                @endif">
                                @if($step['completed'])
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $step['name'] }}</div>
                                @if($step['description'])
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $step['description'] }}</div>
                                @endif
                            </div>
                            @if($step['current'] && !$step['completed'])
                                <button
                                    wire:click="completeStep({{ $loop->index }})"
                                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"
                                >
                                    Selesai
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Progress Updates -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Update Progress</h3>

                <!-- Add Update Form -->
                <form wire:submit="addProgressUpdate" class="mb-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Update Progress
                            </label>
                            <textarea
                                wire:model="progressUpdate"
                                rows="3"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                placeholder="Tulis update progress..."
                                required
                            ></textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                    Progress Percentage
                                </label>
                                <input
                                    type="range"
                                    wire:model="progressPercentage"
                                    min="0"
                                    max="100"
                                    step="5"
                                    class="w-full"
                                >
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
                                    {{ $progressPercentage }}%
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 self-end"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>Tambah Update</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Updates List -->
                <div class="space-y-4">
                    @forelse($progressUpdates as $update)
                        <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    Progress: {{ $update->progress_percentage }}%
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $update->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            @if($update->notes)
                                <div class="text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $update->notes }}
                                </div>
                            @endif
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                Oleh: {{ $update->updater->name ?? 'System' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-zinc-500 dark:text-zinc-400">
                            <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2"></i>
                            <p>Belum ada update progress</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Details -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Detail Pesanan</h3>

                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">User</div>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->user->name }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $order->user->email }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Paket</div>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->package->name ?? 'Tidak Diketahui' }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Project</div>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->project_name }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Domain</div>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->domain_name }}.yourdomain.com</div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Total</div>
                        <div class="text-sm font-medium text-zinc-900 dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Tanggal Pesan</div>
                        <div class="text-sm text-zinc-900 dark:text-white">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Quick Actions</h3>

                <div class="space-y-2">
                    @if($order->status === 'pending')
                        <button
                            wire:click="updateStatus('confirmed')"
                            class="w-full flex items-center gap-2 px-3 py-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg text-sm"
                        >
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Konfirmasi Pesanan
                        </button>
                    @elseif($order->status === 'confirmed')
                        <button
                            wire:click="updateStatus('in_progress')"
                            class="w-full flex items-center gap-2 px-3 py-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg text-sm"
                        >
                            <i data-lucide="play" class="w-4 h-4"></i>
                            Mulai Pengerjaan
                        </button>
                    @elseif($order->status === 'in_progress')
                        <button
                            wire:click="updateStatus('completed')"
                            class="w-full flex items-center gap-2 px-3 py-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg text-sm"
                        >
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Tandai Selesai
                        </button>
                    @endif

                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <button
                            wire:click="updateStatus('cancelled')"
                            class="w-full flex items-center gap-2 px-3 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-sm"
                        >
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            Batalkan Pesanan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        // Initialize Lucide icons
        if (window.Lucide) {
            window.Lucide.createIcons();
        }
    });
</script>
