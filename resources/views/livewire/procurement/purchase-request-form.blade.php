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
                        <select wire:model="warehouse_id" class="w-full rounded-lg border bg-white px-4 py-2.5 text-sm focus:ring-2 outline-none dark:bg-gray-900
                            @error('warehouse_id') border-red-500 focus:border-red-500 focus:ring-red-500/10 dark:border-red-500 @else border-gray-200 focus:border-brand-500 focus:ring-brand-500/10 dark:border-gray-800 @enderror">
                            <option value="">Pilih Gudang</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Pemasok / Supplier (Opsional)</label>
                        <select wire:model="supplier_id" class="w-full rounded-lg border bg-white px-4 py-2.5 text-sm focus:ring-2 outline-none dark:bg-gray-900
                            @error('supplier_id') border-red-500 focus:border-red-500 focus:ring-red-500/10 dark:border-red-500 @else border-gray-200 focus:border-brand-500 focus:ring-brand-500/10 dark:border-gray-800 @enderror">
                            <option value="">-- Tanpa Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-gray-400 mt-1 italic leading-tight">* Memilih supplier sekarang memudahkan penggabungan PR jadi 1 PO nanti.</p>
                        @error('supplier_id')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
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
                    <span wire:loading.remove wire:target="save('submitted')">Submit PR</span>
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
                                        <input type="number" wire:model="rows.{{ $index }}.requested_qty" class="w-full rounded-lg border py-1.5 px-3 text-sm font-bold text-center outline-none dark:bg-gray-800
                                            @error('rows.'.$index.'.requested_qty') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-brand-500 dark:border-gray-800 @enderror">
                                        @error('rows.'.$index.'.requested_qty')
                                            <p class="mt-1.5 flex items-center justify-center gap-1.5 text-[9px] text-red-600 dark:text-red-400">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
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
    <div x-data="{ open: false }" 
         @open-item-modal.window="open = true" 
         @close-item-modal.window="open = false" 
         x-show="open" 
         class="fixed inset-0 z-[1000000] overflow-y-auto font-sans" style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false" 
             class="fixed inset-0 bg-gray-900/35 transition-opacity"></div>

        <!-- Modal Content Container -->
        <div class="flex min-h-screen items-center justify-center p-4" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl rounded-2xl bg-white p-0 shadow-2xl dark:bg-gray-900 border border-white/10 overflow-hidden transform transition-all">
                
                <div class="flex items-center justify-between p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black uppercase tracking-tighter text-gray-900 dark:text-white leading-tight">Cari Perbekalan Farmasi</h3>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">MASTER ITEM OBAT & BMHP</p>
                        </div>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="relative mb-6">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="itemSearch" placeholder="Ketik nama obat atau kode barang..." class="w-full rounded-xl border border-gray-200 bg-gray-50 py-4 pl-12 pr-4 text-sm font-bold focus:bg-white focus:ring-2 focus:ring-emerald-500/20 outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-white transition-all shadow-inner">
                    </div>

                    <div class="max-h-[400px] overflow-y-auto custom-scrollbar pr-2 divide-y divide-gray-50 dark:divide-gray-800">
                        <!-- Loading State -->
                        <div wire:loading wire:target="itemSearch" class="py-12 text-center">
                            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Mencari Data...</p>
                        </div>

                        <!-- Results -->
                        <div wire:loading.remove wire:target="itemSearch">
                            @forelse($searchResults as $item)
                                <button wire:click="addItem({{ $item->id }})" class="w-full flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-all text-left group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center font-black text-sm">
                                            {{ substr($item->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 dark:text-white group-hover:text-emerald-600 transition-colors uppercase tracking-tight">{{ $item->name }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] font-mono text-gray-400 uppercase tracking-tighter">{{ $item->code }}</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                                <span class="text-[10px] font-bold text-gray-500 italic">{{ $item->unit?->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="opacity-0 group-hover:opacity-100 transition-all">
                                        <span class="rounded-lg bg-emerald-500 px-4 py-2 text-[10px] font-black text-white uppercase tracking-widest shadow-lg shadow-emerald-500/20">PILIH</span>
                                    </div>
                                </button>
                            @empty
                                <div class="py-16 text-center text-gray-400">
                                    @if(strlen($itemSearch) >= 2)
                                        <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Maaf, item tidak ditemukan</span>
                                    @else
                                        <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Ketik nama obat untuk mencari</p>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
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
