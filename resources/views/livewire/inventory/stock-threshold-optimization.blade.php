<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-50 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[240px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau kode barang..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all outline-none">
            </div>

            <select wire:model.live="warehouseId" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all">
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Periode:</span>
                <select wire:model.live="lookbackDays" class="bg-transparent border-none font-semibold text-brand-600 outline-none cursor-pointer">
                    <option value="7">7 Hari</option>
                    <option value="30">30 Hari</option>
                    <option value="90">90 Hari</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="applyAll" wire:confirm="Yakin ingin menerapkan semua saran stok untuk filter saat ini?" class="px-4 py-2 bg-brand-50 text-brand-600 border border-brand-100 text-sm font-semibold rounded-xl hover:bg-brand-100 transition-all flex items-center gap-2">
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
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Stok Saat Ini</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Pemakaian/Hari (ADU)</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Batas Sekarang (Min/Max)</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Saran Baru (Min/Max)</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
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
                            <div class="inline-flex px-2 py-1 rounded-lg text-xs font-bold {{ $row['current_stock'] <= $row['suggested_min'] ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                {{ $row['current_stock'] }} {{ $row['item']->unit->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-700">{{ $row['adu'] }}</div>
                            <div class="text-[10px] text-gray-400 italic">berdasarkan {{ $lookbackDays }} hari</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-sm font-medium text-gray-400">
                                {{ $row['current_min'] }} / {{ $row['current_max'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-brand-50 text-brand-600 rounded-lg text-xs font-bold" title="Saran ADU">{{ $row['suggested_min'] }}</span>
                                    <span class="text-gray-300">/</span>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">{{ $row['suggested_max'] }}</span>
                                </div>
                                <button wire:click="applyThreshold({{ $row['item']->id }}, {{ $row['suggested_min_20percent'] }}, {{ $row['suggested_max'] }}, {{ $row['adu'] }})" class="text-[10px] text-gray-400 hover:text-brand-500 underline decoration-dotted">
                                    Pakai Alternatif 20% ({{ $row['suggested_min_20percent'] }})
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="applyThreshold({{ $row['item']->id }}, {{ $row['suggested_min'] }}, {{ $row['suggested_max'] }}, {{ $row['adu'] }})" class="p-2 text-brand-600 hover:bg-brand-50 rounded-lg transition-all" title="Terapkan Saran">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-4 opacity-20"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
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
