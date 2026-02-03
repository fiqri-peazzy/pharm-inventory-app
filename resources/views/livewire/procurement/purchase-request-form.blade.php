<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Header Information -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-5">Informasi Dokumen</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Nomor PR</label>
                        <input type="text" wire:model="request_number" readonly class="w-full rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5 text-sm font-mono font-bold text-indigo-600 dark:border-gray-800 dark:bg-gray-800 dark:text-indigo-400">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Gudang Pemohon</label>
                        <select wire:model="warehouse_id" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10 outline-none dark:border-gray-800 dark:bg-gray-900">
                            <option value="">Pilih Gudang</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id') <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Bulan Periode</label>
                            <select wire:model="period_month" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900">
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Tahun</label>
                            <input type="number" wire:model="period_year" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Tanggal Pengajuan</label>
                        <input type="date" wire:model="request_date" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Catatan Internal</label>
                        <textarea wire:model="notes" rows="3" placeholder="Alasan permintaan atau instruksi khusus..." class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button wire:click="save('draft')" wire:loading.attr="disabled" class="flex-1 rounded-xl border border-gray-200 bg-white py-3.5 text-xs font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 disabled:opacity-50">
                    <span wire:loading.remove wire:target="save('draft')">Simpan Draft</span>
                    <span wire:loading wire:target="save('draft')">Memproses...</span>
                </button>
                <button wire:click="save('submitted')" wire:loading.attr="disabled" class="flex-[1.5] rounded-xl bg-indigo-600 py-3.5 text-xs font-black uppercase tracking-widest text-white hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition-all disabled:opacity-50">
                    <span wire:loading.remove wire:target="save('submitted')">Submit PR 🚀</span>
                    <span wire:loading wire:target="save('submitted')">Mengirim...</span>
                </button>
            </div>
            <a href="{{ route('procurement.requests.index') }}" class="block text-center text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-tighter">Kembali ke Daftar</a>
        </div>

        <!-- Right: Items Table -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col h-full min-h-[600px]">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/30">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white">Daftar Item Permintaan</h3>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase font-bold tracking-tight">Total: {{ count($rows) }} Item Terpilih</p>
                    </div>
                    <button @click="$dispatch('open-item-modal')" class="flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-black text-white hover:bg-emerald-600 transition-all uppercase tracking-widest">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Cari Item
                    </button>
                </div>

                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-3 w-10">No</th>
                                <th class="px-6 py-3">Nama Item / Produk</th>
                                <th class="px-6 py-3 text-center">Stok Saat Ini</th>
                                <th class="px-6 py-3 text-center w-32">Jumlah Minta</th>
                                <th class="px-6 py-3">Keterangan</th>
                                <th class="px-6 py-3 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($rows as $index => $row)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                                    <td class="px-6 py-4 text-xs font-bold text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $row['item_name'] }}</span>
                                            <span class="text-[10px] font-mono text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">{{ $row['item_code'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-mono font-bold @if($row['current_stock'] <= 0) text-red-500 @else text-gray-600 dark:text-gray-400 @endif">
                                            {{ number_format($row['current_stock']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" wire:model="rows.{{ $index }}.requested_qty" class="w-full rounded-lg border border-gray-200 py-1.5 px-3 text-sm font-bold text-center focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-800">
                                        @error('rows.'.$index.'.requested_qty') <span class="text-[9px] text-red-500 block text-center mt-1">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" wire:model="rows.{{ $index }}.notes" placeholder="..." class="w-full bg-transparent border-b border-transparent focus:border-gray-300 py-1 text-xs outline-none dark:text-gray-400 italic">
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="removeRow({{ $index }})" class="p-2 text-gray-300 hover:text-red-500 transition-colors">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center opacity-30">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                            <p class="text-xs font-black uppercase tracking-widest text-gray-500 mb-1">Daftar Item Kosong</p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-tighter">Silakan klik tombol "Cari Item" untuk menambahkan barang</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Search Modal -->
    <div x-data="{ open: @entangle('showItemModal') }" 
         x-show="open" 
         @open-item-modal.window="open = true"
         class="fixed inset-0 z-[1000] overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>

            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-900 border border-white/10">
                <div class="flex items-center justify-between mb-8 border-b border-gray-50 dark:border-gray-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tighter text-gray-900 dark:text-white leading-tight">Cari Perbekalan Farmasi</h3>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Master Item Obat & BMHP</p>
                        </div>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="relative mb-6">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="itemSearch" placeholder="Ketik nama obat atau kode barang (Min. 2 karakter)..." class="w-full rounded-xl border border-gray-200 bg-gray-50 py-4 pl-12 pr-4 text-sm font-bold focus:bg-white focus:ring-2 focus:ring-emerald-500/20 outline-none dark:border-gray-800 dark:bg-gray-800 transition-all">
                </div>

                <div class="max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                    @if(count($searchResults) > 0)
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($searchResults as $item)
                                <button wire:click="addItem({{ $item->id }})" class="flex items-center justify-between p-4 rounded-xl border border-gray-50 bg-white hover:border-emerald-500 hover:bg-emerald-50/30 transition-all text-left group dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-emerald-500/5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white group-hover:text-emerald-600 uppercase tracking-tight">{{ $item->name }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-mono text-gray-400 uppercase tracking-tighter">{{ $item->code }}</span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span class="text-[10px] font-bold text-gray-500 italic">{{ $item->unit?->name }}</span>
                                        </div>
                                    </div>
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="rounded-lg bg-emerald-500 px-3 py-1.5 text-[10px] font-black text-white uppercase tracking-widest shadow-lg shadow-emerald-500/20">Pilih</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @elseif(strlen($itemSearch) >= 2)
                        <div class="py-12 text-center text-gray-400 italic">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span class="text-sm">Item tidak ditemukan.</span>
                        </div>
                    @else
                        <div class="py-12 text-center text-gray-400 italic">
                            <p class="text-xs uppercase font-bold tracking-widest opacity-40">Mulai mengetik untuk mencari...</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
    </style>
</div>
