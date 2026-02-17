<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Help Section -->
    <div class="bg-gradient-to-r from-brand-50 to-indigo-50 border-b border-brand-100 p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center text-white">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-black text-gray-900 mb-2">📊 Panduan Optimasi Batas Stok</h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div class="space-y-2">
                        <p class="font-semibold text-brand-700">🎯 Cara Menggunakan:</p>
                        <ul class="space-y-1 text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-brand-500 font-bold">1.</span>
                                <span><strong>Klik nilai Min/Max/RP</strong> untuk edit langsung</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-brand-500 font-bold">2.</span>
                                <span><strong>Gunakan saran sistem</strong> dengan klik tombol ✓</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-brand-500 font-bold">3.</span>
                                <span><strong>Alternatif 20%</strong> untuk perhitungan lebih simple</span>
                            </li>
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <p class="font-semibold text-brand-700">📖 Penjelasan Istilah:</p>
                        <ul class="space-y-1 text-gray-700 text-xs">
                            <li><strong class="text-amber-600">RP (Reorder Point)</strong>: Titik pemesanan ulang = (ADU
                                × 7 hari) + Safety Stock</li>
                            <li><strong class="text-red-600">MIN (Safety Stock)</strong>: Stok minimum = ADU × 3 hari
                                (buffer darurat)</li>
                            <li><strong class="text-gray-600">MAX</strong>: Stok maksimum = RP + (ADU × 30 hari)</li>
                            <li><strong class="text-blue-600">ADU</strong>: Average Daily Usage (rata-rata pemakaian per
                                hari)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="p-4 border-b border-gray-50 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[240px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau kode barang..."
                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all outline-none">
            </div>

            <select wire:model.live="warehouseId"
                class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all">
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Periode:</span>
                <select wire:model.live="lookbackDays"
                    class="bg-transparent border-none font-semibold text-brand-600 outline-none cursor-pointer">
                    <option value="7">7 Hari</option>
                    <option value="30">30 Hari</option>
                    <option value="90">90 Hari</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="applyAll" wire:confirm="Yakin ingin menerapkan semua saran stok untuk filter saat ini?"
                class="px-4 py-2 bg-brand-50 text-brand-600 border border-brand-100 text-sm font-semibold rounded-xl hover:bg-brand-100 transition-all flex items-center gap-2">
                Setujui Semua Saran
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 mb-4 bg-green-50 text-green-700 text-sm font-medium border-l-4 border-green-500 mx-4 mt-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Barang</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Stok Saat Ini
                    </th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Pemakaian/Hari
                        (ADU)</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">
                        Batas Sekarang<br><span class="text-[9px] font-normal text-gray-400">(Min / Max / RP)</span>
                    </th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">
                        Saran Sistem<br><span class="text-[9px] font-normal text-gray-400">(Min / Max)</span></th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($displayData as $row)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $row['item']->name }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">{{ $row['item']->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="inline-flex px-2 py-1 rounded-lg text-xs font-bold {{ $row['current_stock'] <= $row['suggested_min'] ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                {{ $row['current_stock'] }} {{ $row['item']->unit->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-700">{{ $row['adu'] }}</div>
                            <div class="text-[10px] text-gray-400 italic">berdasarkan {{ $lookbackDays }} hari</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($editingItemId === $row['item']->id)
                                <!-- Edit Mode -->
                                <div class="flex flex-col gap-2 items-center">
                                    <div class="flex items-center gap-1">
                                        <input wire:model="editMin" type="number"
                                            class="w-16 px-2 py-1 text-xs border border-red-300 rounded focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                            placeholder="Min">
                                        <span class="text-gray-300">/</span>
                                        <input wire:model="editMax" type="number"
                                            class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                                            placeholder="Max">
                                        <span class="text-gray-300">/</span>
                                        <input wire:model="editRP" type="number"
                                            class="w-16 px-2 py-1 text-xs border border-amber-300 rounded focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                                            placeholder="RP">
                                    </div>
                                    <div class="flex gap-1">
                                        <button wire:click="updateThreshold({{ $row['item']->id }})"
                                            class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition-all">
                                            Simpan
                                        </button>
                                        <button wire:click="cancelEdit"
                                            class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300 transition-all">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- View Mode - Clickable -->
                                <button
                                    wire:click="startEdit({{ $row['item']->id }}, {{ $row['current_min'] }}, {{ $row['current_max'] }}, {{ $row['current_rp'] }})"
                                    class="group hover:bg-gray-100 px-3 py-2 rounded-lg transition-all">
                                    <div class="flex items-center gap-2 justify-center">
                                        <span
                                            class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs font-bold group-hover:bg-red-100"
                                            title="Min Stock">{{ $row['current_min'] }}</span>
                                        <span class="text-gray-300">/</span>
                                        <span
                                            class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold group-hover:bg-gray-200"
                                            title="Max Stock">{{ $row['current_max'] }}</span>
                                        <span class="text-gray-300">/</span>
                                        <span
                                            class="px-2 py-1 bg-amber-50 text-amber-600 rounded text-xs font-bold group-hover:bg-amber-100"
                                            title="Reorder Point">{{ $row['current_rp'] }}</span>
                                    </div>
                                    <div
                                        class="text-[9px] text-gray-400 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        Klik untuk edit
                                    </div>
                                </button>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-brand-50 text-brand-600 rounded-lg text-xs font-bold"
                                        title="Saran ADU">{{ $row['suggested_min'] }}</span>
                                    <span class="text-gray-300">/</span>
                                    <span
                                        class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">{{ $row['suggested_max'] }}</span>
                                </div>
                                <button
                                    wire:click="applyThreshold({{ $row['item']->id }}, {{ $row['suggested_min_20percent'] }}, {{ $row['suggested_max'] }}, {{ $row['suggested_rp'] }}, {{ $row['adu'] }})"
                                    class="text-[10px] text-gray-400 hover:text-brand-500 underline decoration-dotted">
                                    Pakai Alternatif 20% ({{ $row['suggested_min_20percent'] }})
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button
                                wire:click="applyThreshold({{ $row['item']->id }}, {{ $row['suggested_min'] }}, {{ $row['suggested_max'] }}, {{ $row['suggested_rp'] }}, {{ $row['adu'] }})"
                                class="p-2 text-brand-600 hover:bg-brand-50 rounded-lg transition-all"
                                title="Terapkan Saran">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1" class="mb-4 opacity-20">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <p>Tidak ada data barang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-50">
        {{ $items->links() }}
    </div>
</div>