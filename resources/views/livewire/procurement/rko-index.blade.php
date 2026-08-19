<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Perencanaan RKO</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Rencana Kebutuhan Obat & Alkes Berbasis ABC-VEN</p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="syncUsage" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition-colors border border-brand-200">
                <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                <svg wire:loading class="animate-spin h-5 w-5 mr-2 text-brand-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Sync Data Pemakaian
            </button>
            <a href="{{ route('procurement.rko.print', ['warehouse_id' => $warehouseId, 'projection_days' => $projectionDays, 'search' => $search, 'ven' => $filterVen, 'abc' => $filterAbc]) }}"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Cetak RKO
            </a>

            <div class="flex gap-2">
                <button wire:click="generateBatchSp('reguler')"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-sm text-sm font-semibold dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                    Buat SP Reguler
                </button>
                <button wire:click="generateBatchSp('narkotika')"
                    class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg hover:bg-red-100 transition-colors shadow-sm text-sm font-semibold">
                    Buat SP Narkotika
                </button>
                <button wire:click="generateBatchSp('psikotropika')"
                    class="inline-flex items-center px-4 py-2 bg-purple-50 border border-purple-200 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors shadow-sm text-sm font-semibold">
                    Buat SP Psikotropika
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-end dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">Cari Obat</label>
            <input wire:model.live="search" type="text" placeholder="Nama atau kode obat..."
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:border-gray-800">
        </div>
        <div class="w-48">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">Unit/Gudang</label>
            <select wire:model.live="warehouseId"
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:border-gray-800">
                <option value="all">KONSOLIDASI (GLOBAL)</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-24">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">Proyeksi</label>
            <select wire:model.live="projectionDays"
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:border-gray-800">
                <option value="7">7 Hari</option>
                <option value="30">30 Hari</option>
                <option value="60">60 Hari</option>
                <option value="90">90 Hari</option>
            </select>
        </div>
        <div class="w-32">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">Kategori VEN</label>
            <select wire:model.live="filterVen"
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:border-gray-800">
                <option value="">Semua VEN</option>
                <option value="V">Vital</option>
                <option value="E">Essensial</option>
                <option value="N">Non-Essensial</option>
            </select>
        </div>
        <div class="w-32">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">Kategori ABC</label>
            <select wire:model.live="filterAbc"
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:border-gray-800">
                <option value="">Semua ABC</option>
                <option value="A">Class A</option>
                <option value="B">Class B</option>
                <option value="C">Class C</option>
            </select>
        </div>
    </div>

    @if (session()->has('success'))
        <div
            class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- SUGGESTIONS TABLE -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-white/[0.03]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Item &
                        Klasifikasi</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Status Stok</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Rata-rata Harian</th>
                    <th
                        class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider bg-brand-50 text-brand-700 dark:text-gray-400">
                        Usulan RKO</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Urgency</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100 dark:bg-white/[0.03] dark:divide-gray-800">
                @foreach($displayData as $row)
                    <tr class="hover:bg-gray-50 transition-colors dark:hover:bg-white/[0.03]">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @php
                                        $venColor = match ($row['ven']) { 'V' => 'bg-red-100 text-red-700', 'E' => 'bg-amber-100 text-amber-700', 'N' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600'};
                                        $abcColor = match ($row['abc']) { 'A' => 'border-purple-200 text-purple-700 bg-purple-50', 'B' => 'border-indigo-200 text-indigo-700 bg-indigo-50', 'C' => 'border-gray-200 text-gray-700 bg-gray-50'};
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-full font-bold text-xs {{ $venColor }} mr-2">{{ $row['ven'] }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $row['item_name'] }}</div>
                                    <div class="flex items-center text-xs text-gray-500 mt-0.5 dark:text-gray-400">
                                        <span class="mr-2">{{ $row['code'] }}</span>
                                        <span class="px-1.5 py-0.5 border rounded-md font-medium {{ $abcColor }}">Class
                                            {{ $row['abc'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format($row['total_stock'] ?? $row['current_stock']) }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">Unit Tersedia</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format($row['total_avg_usage'] ?? $row['avg_usage'], 2) }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">Usage/Hari</div>
                        </td>
                        <td class="px-6 py-4 text-right bg-brand-50/30">
                            <div class="text-lg font-bold text-brand-700">{{ number_format($row['suggested_qty']) }}</div>
                            <div class="text-[10px] text-brand-600 font-medium uppercase tracking-tighter">Recommended Order
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColor = match ($row['urgency_level']) {
                                    'OUT_OF_STOCK' => 'bg-red-600 text-white animate-pulse',
                                    'CRITICAL' => 'bg-red-100 text-red-600 border border-red-200',
                                    'WARNING' => 'bg-amber-100 text-amber-600 border border-amber-200',
                                    default => 'bg-green-100 text-green-600 border border-green-200'
                                };
                                $label = match ($row['urgency_level']) {
                                    'OUT_OF_STOCK' => 'KOSONG!',
                                    'CRITICAL' => 'KRITIS',
                                    'WARNING' => 'WASPADA',
                                    default => 'AMAN'
                                };
                            @endphp
                            <span
                                class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide {{ $statusColor }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button
                                wire:click="generateSp({{ $row['item_id'] }}, {{ $row['suggested_qty'] }}, '{{ $row['item_category_code'] }}')"
                                class="inline-flex items-center text-brand-600 hover:text-brand-900 font-semibold text-xs transition-colors">
                                Buat SP
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
                @if($displayData->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic dark:text-gray-400">
                            Tidak ada usulan pengadaan ditemukan untuk filter ini.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
            class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-2xl shadow-lg shadow-red-200 overflow-hidden relative group">
            <div class="relative z-10 text-white">
                <div class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Items Out of Stock</div>
                <div class="text-3xl font-black">{{ $displayData->where('urgency_level', 'OUT_OF_STOCK')->count() }}
                </div>
                <div class="mt-4 flex items-center text-xs font-semibold">
                    <span class="bg-white/20 px-2 py-1 rounded-full mr-2">ACTION REQUIRED</span>
                </div>
            </div>
            <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-white opacity-10 group-hover:scale-110 transition-transform duration-500"
                fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center dark:bg-white/[0.03] dark:border-gray-800">
            <div class="p-3 bg-amber-50 rounded-xl mr-4">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest dark:text-gray-400">Urgent (VEN=V)</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $displayData->where('ven', 'V')->count() }} Item</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center dark:bg-white/[0.03] dark:border-gray-800">
            <div class="p-3 bg-brand-50 rounded-xl mr-4">
                <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest dark:text-gray-400">Total Usulan</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($displayData->sum('suggested_qty')) }}
                    Unit</div>
            </div>
        </div>
    </div>
</div>