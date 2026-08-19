<div class="space-y-4">
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-gray-100 shadow-sm dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path
                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                    </path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight leading-none dark:text-white">Manajemen &
                    Monitoring Batch</h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 block dark:text-gray-500">Inventory Expiry
                    & Shelf-life Control</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div
                class="bg-red-50 text-red-600 px-3 py-1.5 rounded-xl border border-red-100 text-[10px] font-black uppercase tracking-widest dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20">
                {{ \App\Models\ItemBatch::expired()->count() }} Expired
            </div>
            <div
                class="bg-amber-50 text-amber-600 px-3 py-1.5 rounded-xl border border-amber-100 text-[10px] font-black uppercase tracking-widest dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20">
                {{ \App\Models\ItemBatch::nearExpired()->count() }} Near Expired
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4 dark:bg-white/[0.03] dark:border-gray-800">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block dark:text-gray-500">Cari
                    (Item/Batch)</label>
                <input type="text" wire:model.live="search" placeholder="Cari nama barang atau nomor batch..."
                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-amber-500 focus:border-amber-500 transition-all font-medium dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
            </div>

            @if(auth()->user()->hasAnyRole(['super-admin', 'kepala-farmasi', 'direktur', 'bupati', 'auditor']))
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block dark:text-gray-500">Filter
                        Gudang</label>
                    <select wire:model.live="warehouseId"
                        class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-amber-500 focus:border-amber-500 transition-all font-bold text-gray-600 dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-300">
                        <option value="">Semua Warehouses</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block dark:text-gray-500">Gudang /
                        Station</label>
                    <div
                        class="w-full bg-gray-100 border-gray-200 rounded-xl text-sm px-4 py-3 font-black text-gray-400 italic leading-tight flex items-center h-[46px] dark:bg-gray-800 dark:border-gray-800 dark:text-gray-500">
                        {{ auth()->user()->warehouse->name ?? 'Locked' }}
                    </div>
                </div>
            @endif

            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block dark:text-gray-500">Status
                    Kedaluwarsa</label>
                <select wire:model.live="status"
                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-amber-500 focus:border-amber-500 transition-all font-bold text-gray-600 dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-300">
                    <option value="">Semua Batch</option>
                    <option value="active">Active (Masih Bagus)</option>
                    <option value="near_expired">Mendekati ED (< 90 hari)</option>
                    <option value="expired">Sudah ED (Expired)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 dark:bg-white/[0.02] dark:border-gray-800">
                        <th class="px-6 py-4 font-black text-gray-400 uppercase dark:text-gray-500">Item / Produk</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase dark:text-gray-500">Warehouse / Lokasi</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center dark:text-gray-500">Nomor Batch</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-40 dark:text-gray-500">Tgl Kedaluwarsa (ED)
                        </th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-32 dark:text-gray-500">Stok Saat Ini</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-right dark:text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-gray-50/50 transition-colors dark:hover:bg-white/[0.03]">
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 block leading-tight dark:text-white">{{ $batch->item->name }}</span>
                                <span
                                    class="text-[9px] text-gray-400 font-bold uppercase italic tracking-widest dark:text-gray-500">{{ $batch->item->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-600 dark:text-gray-300">{{ $batch->warehouse->name }}</span>
                                <span
                                    class="text-[9px] text-gray-400 font-bold uppercase block tracking-tighter italic dark:text-gray-500">{{ $batch->warehouse->type }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20">
                                    {{ $batch->batch_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="font-black {{ $batch->expired_date <= now() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} block mb-0.5">
                                    {{ $batch->expired_date->format('d M Y') }}
                                </span>
                                <span class="text-[9px] font-black text-gray-400 uppercase italic dark:text-gray-500">
                                    @if($batch->expired_date <= now())
                                        Expired {{ $batch->expired_date->diffForHumans() }}
                                    @else
                                        Ends in {{ $batch->expired_date->diffForHumans(['parts' => 1]) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="text-xl font-black text-gray-900 tracking-tighter dark:text-white">{{ number_format($batch->current_qty) }}</span>
                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Sisa
                                        Unit</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php $status = $batch->status_label; @endphp
                                <div class="flex flex-col items-end gap-1">
                                    <span
                                        class="px-2.5 py-1 rounded-lg font-black text-[9px] uppercase border shadow-sm {{ $status->color }}">
                                        {{ $status->label }}
                                    </span>
                                    <div class="flex items-center gap-1 group">
                                        @if($batch->warehouse->is_main)
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                            <span
                                                class="text-[8px] font-black italic text-gray-400 uppercase tracking-tighter dark:text-gray-500">Main
                                                Warehouse Stock</span>
                                        @else
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                                            <span
                                                class="text-[8px] font-black italic text-gray-400 uppercase tracking-tighter dark:text-gray-500">Depo
                                                / Unit Stock</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center italic text-gray-300 dark:text-gray-700">Data batch tidak ditemukan
                                untuk kriteria ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-50 dark:bg-white/[0.02] dark:border-gray-800">
            {{ $batches->links() }}
        </div>
    </div>
</div>