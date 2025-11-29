<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Upload File</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Kelola file untuk pesanan</p>
        </div>
    </div>

    <!-- Upload Area -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="max-w-2xl mx-auto">
            <!-- Dropzone -->
            <div
                x-data="{ isDragging: false }"
                class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-8 text-center hover:border-zinc-400 dark:hover:border-zinc-500 transition-colors"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="isDragging = false; $wire.uploadMultiple('files', $event.dataTransfer.files)"
                :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/20': isDragging }"
            >
                <i data-lucide="upload-cloud" class="w-12 h-12 text-zinc-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Drop files here</h3>
                <p class="text-zinc-500 dark:text-zinc-400 mb-4">or</p>

                <label class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Pilih File
                    <input
                        type="file"
                        class="hidden"
                        multiple
                        wire:model="files"
                    >
                </label>

                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-4">
                    Supports: JPG, PNG, PDF, DOC, DOCX, ZIP, RAR<br>
                    Max file size: 10MB
                </p>
            </div>

            <!-- Upload Progress -->
            @if($uploadProgress > 0 && $uploadProgress < 100)
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                        <span>Uploading...</span>
                        <span>{{ $uploadProgress }}%</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                        <div
                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            style="width: {{ $uploadProgress }}%"
                        ></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Uploaded Files -->
    @if($uploadedFiles->count() > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white">File Terupload</h3>
            </div>

            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($uploadedFiles as $file)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <!-- File Icon -->
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

                            <!-- File Info -->
                            <div>
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $file->name }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $file->size }} • {{ $file->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <!-- Download -->
                            <a
                                href="{{ $file->url }}"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                title="Download"
                            >
                                <i data-lucide="download" class="w-4 h-4"></i>
                            </a>

                            <!-- Delete -->
                            <button
                                wire:click="deleteFile({{ $file->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus file ini?"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
                                title="Hapus"
                            >
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
