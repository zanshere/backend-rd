<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Detail Pesanan #{{ $order->order_number }}</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Informasi lengkap tentang pesanan Anda</p>
        </div>

        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <!-- Back Button -->
            <a
                href="{{ route('user.orders') }}"
                class="flex items-center gap-2 px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-300 dark:border-zinc-600 rounded-lg"
            >
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>

            <!-- Status Badge -->
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                @elseif($order->status === 'accepted') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                @elseif($order->status === 'progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                @elseif($order->status === 'revision') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                @elseif($order->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                @endif">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Progress -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Progress Pesanan</h3>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                        <span>Progress Pengerjaan</span>
                        <span>{{ $order->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                        <div
                            class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                            style="width: {{ $order->progress_percentage }}%"
                        ></div>
                    </div>
                </div>

                <!-- Progress Timeline -->
                <div class="space-y-4">
                    @foreach($progressSteps as $step)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium mt-1
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
                                @if($step['date'])
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ $step['date'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Progress Updates -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Update Progress</h3>

                <div class="space-y-4">
                    @foreach($order->progressUpdates->sortByDesc('created_at') as $update)
                        <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    Progress: {{ $update->percentage }}%
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $update->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <div class="text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $update->description }}
                            </div>
                            @if($update->admin)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">
                                    Oleh: {{ $update->admin->name }}
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if($order->progressUpdates->count() === 0)
                        <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            <i data-lucide="clock" class="w-12 h-12 mx-auto mb-3"></i>
                            <p>Belum ada update progress</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Requirements -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Requirements & Files</h3>

                <div class="space-y-4">
                    @if($order->requirements)
                        <div>
                            <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Kebutuhan Website:</h4>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-700/50 p-3 rounded-lg">
                                {{ $order->requirements }}
                            </div>
                        </div>
                    @endif

                    <!-- Uploaded Files -->
                    @if($order->files->count() > 0)
                        <div>
                            <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">File Terupload:</h4>
                            <div class="space-y-2">
                                @foreach($order->files as $file)
                                    <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                                                @if(in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <i data-lucide="image" class="w-5 h-5 text-blue-600"></i>
                                                @elseif(in_array($file->extension, ['pdf']))
                                                    <i data-lucide="file-text" class="w-5 h-5 text-red-600"></i>
                                                @elseif(in_array($file->extension, ['doc', 'docx']))
                                                    <i data-lucide="file" class="w-5 h-5 text-blue-600"></i>
                                                @elseif(in_array($file->extension, ['zip', 'rar']))
                                                    <i data-lucide="archive" class="w-5 h-5 text-orange-600"></i>
                                                @else
                                                    <i data-lucide="file" class="w-5 h-5 text-zinc-600"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $file->name }}</div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $file->size }}</div>
                                            </div>
                                        </div>
                                        <a
                                            href="{{ $file->url }}"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                        >
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Upload New Files -->
                    @if(in_array($order->status, ['accepted', 'progress', 'revision']))
                        <div>
                            <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Upload File Tambahan:</h4>
                            <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 text-center">
                                <input
                                    type="file"
                                    wire:model="newFiles"
                                    multiple
                                    class="hidden"
                                    id="fileUpload"
                                >
                                <label for="fileUpload" class="cursor-pointer">
                                    <i data-lucide="upload-cloud" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Klik untuk upload file tambahan</p>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Max 10MB per file</p>
                                </label>
                            </div>
                            @if($newFiles)
                                <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ count($newFiles) }} file dipilih
                                </div>
                            @endif
                            <button
                                wire:click="uploadFiles"
                                wire:loading.attr="disabled"
                                class="mt-3 w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50"
                            >
                                <span wire:loading.remove>Upload Files</span>
                                <span wire:loading>Uploading...</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Ringkasan Pesanan</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Paket</span>
                        <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->package->name }}</span>
                    </div>

                    @if($order->custom_package_name)
                        <div class="flex justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Custom Package</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->custom_package_name }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Tanggal Pesan</span>
                        <span class="text-sm text-zinc-900 dark:text-white">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>

                    @if($order->deadline)
                        <div class="flex justify-between">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">Deadline</span>
                            <span class="text-sm text-zinc-900 dark:text-white">{{ $order->deadline->format('d M Y') }}</span>
                        </div>
                    @endif

                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-medium text-zinc-900 dark:text-white">Total</span>
                            <span class="text-base font-bold text-zinc-900 dark:text-white">{{ $order->display_total_price }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Aksi</h3>

                <div class="space-y-2">
                    @if($order->status === 'pending')
                        <button
                            wire:click="cancelOrder"
                            wire:confirm="Apakah Anda yakin ingin membatalkan pesanan ini?"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Batalkan Pesanan
                        </button>
                    @endif

                    @if($order->status === 'completed')
                        <button
                            wire:click="downloadAllFiles"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg"
                        >
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Download Semua File
                        </button>
                    @endif

                    @if(in_array($order->status, ['progress', 'revision']))
                        <button
                            wire:click="requestRevision"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg"
                        >
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            Minta Revisi
                        </button>
                    @endif

                    <a
                        href="{{ route('user.messages') }}?order={{ $order->id }}"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
                    >
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        Hubungi Admin
                    </a>
                </div>
            </div>

            <!-- Admin Contact -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Butuh Bantuan?</h3>

                <div class="space-y-3">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Jika Anda memiliki pertanyaan tentang pesanan ini, jangan ragu untuk menghubungi admin.
                    </p>

                    <a
                        href="{{ route('user.messages') }}?order={{ $order->id }}"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 border border-zinc-300 dark:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-lg text-sm"
                    >
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        Kirim Pesan
                    </a>
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
