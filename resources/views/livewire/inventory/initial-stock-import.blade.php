<div>
    <div class="p-6">
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Import Stok Awal </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Modul untuk mengimpor data saldo stok terakhir dari sistem lama ke Gudang Utama sebagai
                        transaksi penerimaan resmi.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- STEP 1: Download Template -->
                <div class="xl:col-span-1">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden h-full flex flex-col">
                        <div
                            class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                                    1</div>
                                <h2 class="text-base font-semibold text-blue-900 dark:text-blue-300">Download Template
                                </h2>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-4 flex-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Download template Excel yang sudah berisi daftar semua item dari master data sistem. Isi
                                kolom yang diperlukan sesuai panduan.
                            </p>
                            <ul class="space-y-2 text-xs text-gray-500 dark:text-gray-400">
                                <li class="flex gap-2 items-start"><span class="text-blue-500 mt-0.5">✓</span> Daftar
                                    item diambil dari master data sistem</li>
                                <li class="flex gap-2 items-start"><span class="text-blue-500 mt-0.5">✓</span> Sheet
                                    referensi supplier & gudang tersedia</li>
                                <li class="flex gap-2 items-start"><span class="text-blue-500 mt-0.5">✓</span> Panduan
                                    pengisian lengkap di sheet PANDUAN</li>
                                <li class="flex gap-2 items-start"><span class="text-blue-500 mt-0.5">✓</span> Isi
                                    <strong>batch_number</strong>, <strong>expired_date</strong>,
                                    <strong>qty_received</strong>, <strong>qty_used</strong>
                                </li>
                            </ul>
                            <div class="mt-auto">
                                <button wire:click="downloadTemplate" wire:loading.attr="disabled"
                                    wire:target="downloadTemplate"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-150 disabled:opacity-70">
                                    <span wire:loading.remove wire:target="downloadTemplate">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        Download Template Excel
                                    </span>
                                    <span wire:loading wire:target="downloadTemplate" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Menyiapkan file...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Upload & Import -->
                <div class="xl:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div
                            class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-green-50 dark:bg-green-900/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">
                                    2</div>
                                <h2 class="text-base font-semibold text-green-900 dark:text-green-300">Upload & Proses
                                    Import</h2>
                            </div>
                        </div>
                        <div class="p-5">
                            <form wire:submit.prevent="import" class="space-y-5">
                                <!-- Drop Zone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File
                                        Template yang sudah diisi (.xlsx)</label>
                                    <label for="file-upload"
                                        class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-xl cursor-pointer transition-colors
                                        {{ $file ? 'border-green-400 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600' }}">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            @if ($file)
                                                <svg class="w-8 h-8 text-green-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                    {{ $file->getClientOriginalName() }}</p>
                                                <p class="text-xs text-gray-400">Klik untuk ganti file</p>
                                            @else
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                    </path>
                                                </svg>
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><span
                                                        class="font-semibold">Klik untuk upload</span> atau drag & drop
                                                </p>
                                                <p class="text-xs text-gray-400">Format XLSX, maksimal 10MB</p>
                                            @endif
                                        </div>
                                        <input id="file-upload" type="file" wire:model="file" class="hidden"
                                            accept=".xlsx,.xls">
                                    </label>
                                    <div wire:loading wire:target="file"
                                        class="text-xs text-blue-500 animate-pulse mt-1 flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Mengupload file...
                                    </div>
                                    @error('file')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Warning box -->
                                <div
                                    class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3 rounded-lg text-xs text-amber-700 dark:text-amber-300">
                                    <strong>Perhatian:</strong> Pastikan data pada template sudah benar sebelum
                                    diproses.
                                    Setiap baris akan dibuatkan transaksi <strong>Penerimaan Barang</strong> resmi ke
                                    Gudang Utama.
                                    Baris dengan <code>qty_received = 0</code> akan diabaikan.
                                </div>

                                <!-- Submit -->
                                <button type="submit" :disabled="{{ $isImporting ? 'true' : 'false' }}"
                                    wire:loading.attr="disabled" wire:target="import"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all duration-150 disabled:opacity-70">
                                    <span wire:loading.remove wire:target="import">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                            </path>
                                        </svg>
                                        Proses Import ke Sistem
                                    </span>
                                    <span wire:loading wire:target="import" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Sedang memproses, mohon tunggu...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Results -->
                    @if ($importResults)
                        <div class="mt-4">
                            @if ($importResults['status'] === 'success')
                                <div
                                    class="p-5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-6 h-6 text-green-500 shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="font-bold text-green-800 dark:text-green-300 text-lg">Import
                                                Berhasil!</p>
                                            <div class="mt-3 grid grid-cols-3 gap-3">
                                                <div
                                                    class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center border border-green-100 dark:border-green-900">
                                                    <p class="text-2xl font-bold text-green-600">
                                                        {{ $importResults['receivings'] }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">Dokumen Penerimaan</p>
                                                </div>
                                                <div
                                                    class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center border border-green-100 dark:border-green-900">
                                                    <p class="text-2xl font-bold text-blue-600">
                                                        {{ $importResults['imported'] }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">Item Berhasil</p>
                                                </div>
                                                <div
                                                    class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center border border-{{ $importResults['skipped'] > 0 ? 'orange' : 'green' }}-100">
                                                    <p
                                                        class="text-2xl font-bold text-{{ $importResults['skipped'] > 0 ? 'orange' : 'gray' }}-600">
                                                        {{ $importResults['skipped'] }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">Item Diabaikan</p>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex flex-wrap gap-3">
                                                <a href="/procurement/receivings"
                                                    class="inline-flex items-center gap-1 text-sm font-semibold text-green-800 dark:text-green-300 hover:underline">
                                                    Lihat Penerimaan Barang &rarr;
                                                </a>
                                                <a href="/inventory/stocks/batches"
                                                    class="inline-flex items-center gap-1 text-sm font-semibold text-blue-700 dark:text-blue-400 hover:underline">
                                                    Lihat Monitoring Batch &rarr;
                                                </a>
                                            </div>
                                            @if (count($importResults['errors']) > 0)
                                                <div
                                                    class="mt-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 border border-amber-200">
                                                    <p class="text-xs font-semibold text-amber-700 mb-2">Peringatan /
                                                        Baris yang dilewati:</p>
                                                    @foreach ($importResults['errors'] as $err)
                                                        <p class="text-xs text-amber-600">• {{ $err }}</p>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-bold text-red-800 dark:text-red-300">Gagal!</p>
                                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                            {{ $importResults['message'] }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
