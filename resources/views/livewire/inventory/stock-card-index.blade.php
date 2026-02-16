<div class="space-y-4">
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight leading-none">Kartu Stok & Mutasi
                </h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 block">Full
                    Traceability & Movement Monitoring</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inwards</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Outwards</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Cari Item
                    (Nama/Kode)</label>
                <input type="text" wire:model.live="search" placeholder="Cari obat atau BMHP..."
                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium">
            </div>

            @if(auth()->user()->hasAnyRole(['super-admin', 'kepala-farmasi', 'direktur', 'bupati', 'auditor']))
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Gudang /
                        Station</label>
                    <select wire:model.live="warehouseId"
                        class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold text-gray-600">
                        <option value="">Semua Warehouses</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Gudang /
                        Station</label>
                    <div
                        class="w-full bg-gray-100 border-gray-200 rounded-xl text-sm px-4 py-3 font-black text-gray-400 italic">
                        {{ auth()->user()->warehouse->name ?? 'Locked' }}
                    </div>
                </div>
            @endif

            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Jenis
                    Transaksi</label>
                <select wire:model.live="transactionType"
                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold text-gray-600">
                    <option value="">Semua Transaksi</option>
                    <option value="receiving">Penerimaan (Receiving)</option>
                    <option value="distribution_in">Distribusi Masuk</option>
                    <option value="distribution_out">Distribusi Keluar</option>
                    <option value="adjustment">Penyesuaian (Adjustment)</option>
                    <option value="prescription">Resep / Pemakaian</option>
                    <option value="disposal">Pemusnahan</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Dari
                        Tanggal</label>
                    <input type="date" wire:model.live="startDate"
                        class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold text-gray-600">
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Sampai
                        Tanggal</label>
                    <input type="date" wire:model.live="endDate"
                        class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold text-gray-600">
                </div>
            </div>
            <div class="flex items-end pb-1 text-[10px] font-bold text-gray-400 italic">
                * Menampilkan riwayat mutasi stok secara kronologis (Terbaru di atas).
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 font-black text-gray-400 uppercase">Waktu & Transaksi</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase">Item / Produk</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase">Batch & Warehouse</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-24">In</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-24">Out</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-right w-32">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($stockCards as $card)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span
                                    class="font-black text-gray-900 block leading-none mb-1">{{ $card->transaction_date->format('d/m/Y') }}</span>
                                <div class="flex flex-col gap-1 mt-1.5">
                                    @php
                                        $statusClass = match ($card->transaction_type) {
                                            'receiving' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'distribution_in' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'distribution_out' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'prescription', 'dispensing' => 'bg-purple-50 text-purple-600 border-purple-100',
                                            'adjustment' => 'bg-red-50 text-red-600 border-red-100',
                                            'stock_opname' => 'bg-gray-50 text-gray-600 border-gray-100',
                                            default => 'bg-gray-50 text-gray-500 border-gray-100'
                                        };

                                        $icon = match ($card->transaction_type) {
                                            'receiving' => '<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path></svg>',
                                            'distribution_in' => '<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>',
                                            'distribution_out' => '<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>',
                                            'prescription', 'dispensing' => '<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
                                            default => ''
                                        };
                                    @endphp

                                    <div class="flex items-center gap-1.5">
                                        <span
                                            class="text-[9px] font-black uppercase tracking-widest {{ $statusClass }} px-2 py-1 rounded-md border flex items-center gap-1 shadow-sm">
                                            {!! $icon !!}
                                            {{ str_replace('_', ' ', $card->transaction_type) }}
                                        </span>
                                    </div>

                                    <!-- Detail Asal Usul (Reference Data) -->
                                    <div class="flex flex-col gap-0.5 mt-1 border-l-2 border-gray-100 pl-2">
                                        @if($card->transaction_type == 'receiving' && $card->reference)
                                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-tight">PBF:
                                                {{ $card->reference->supplier->name ?? 'N/A' }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 italic">No:
                                                {{ $card->reference->receiving_number }}</span>
                                        @elseif($card->transaction_type == 'distribution_in' && $card->reference)
                                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-tight">DARI:
                                                {{ $card->reference->origin->name ?? 'GUDANG' }}</span>
                                        @elseif($card->transaction_type == 'distribution_out' && $card->reference)
                                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-tight">KE:
                                                {{ $card->reference->destination->name ?? 'STATION' }}</span>
                                        @elseif(($card->transaction_type == 'prescription' || $card->transaction_type == 'dispensing') && $card->reference)
                                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-tight">PASIEN:
                                                {{ $card->reference->patient_name ?? 'UMUM' }}</span>
                                            <span class="text-[8px] font-bold text-gray-400 italic">Rx:
                                                {{ $card->reference->prescription_number }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 block">{{ $card->item->name }}</span>
                                <span
                                    class="text-[9px] text-gray-400 font-bold uppercase italic tracking-widest">{{ $card->item->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100 w-fit">Batch:
                                        {{ $card->batch->batch_number ?? '-' }}</span>
                                    <span
                                        class="text-[9px] font-black text-gray-400 uppercase italic">{{ $card->warehouse->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($card->qty_in > 0)
                                    <span
                                        class="text-lg font-black text-emerald-600 italic">+{{ number_format($card->qty_in) }}</span>
                                @else
                                    <span class="text-gray-200">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($card->qty_out > 0)
                                    <span
                                        class="text-lg font-black text-amber-500 italic">-{{ number_format($card->qty_out) }}</span>
                                @else
                                    <span class="text-gray-200">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-xl font-black text-gray-900 tracking-tighter">{{ number_format($card->last_stock) }}</span>
                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Running
                                        Balance</span>
                                </div>
                            </td>
                        </tr>
                        @if($card->notes)
                            <tr class="bg-gray-50/20">
                                <td colspan="6" class="px-6 py-2 border-b border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="3" class="text-gray-300">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                        <span class="text-[9px] font-medium text-gray-400 italic">Catatan:
                                            {{ $card->notes }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center italic text-gray-300">Belum ada data mutasi stok
                                dalam periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-50">
            {{ $stockCards->links() }}
        </div>
    </div>
</div>