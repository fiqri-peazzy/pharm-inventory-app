<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.distributions.index') }}" class="w-10 h-10 bg-white dark:bg-white/[0.03] border border-gray-100 dark:border-gray-800 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-brand-500 hover:border-brand-500 transition-all shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Buat Permintaan Stok</h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mt-1 block">Internal Requisition (LPLPO Internal)</span>
            </div>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 dark:bg-red-500/15 border border-red-100 dark:border-red-500/20 text-red-600 dark:text-red-400 rounded-xl text-xs font-bold uppercase italic">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Warehouse Selection -->
        <div class="bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-gray-800 pb-3">Konfigurasi Gudang</h3>
            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5 block">Gudang Sumber (Origin)</label>
                <select wire:model="origin_warehouse_id" class="w-full bg-gray-50 dark:bg-white/[0.03] border rounded-xl text-sm px-4 py-3 focus:ring-2 transition-all font-bold text-gray-700 dark:text-gray-300
                    @error('origin_warehouse_id') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-100 focus:ring-brand-500 focus:border-brand-500 dark:border-gray-800 @enderror">
                    <option value="">Pilih Gudang Sumber</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
                @error('origin_warehouse_id')
                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5 block">Unit/Depo Peminta (Destination)</label>
                <select wire:model="destination_warehouse_id" class="w-full bg-gray-50 dark:bg-white/[0.03] border rounded-xl text-sm px-4 py-3 focus:ring-2 transition-all font-bold text-gray-700 dark:text-gray-300
                    @error('destination_warehouse_id') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-100 focus:ring-brand-500 focus:border-brand-500 dark:border-gray-800 @enderror">
                    <option value="">Pilih Unit Peminta</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
                @error('destination_warehouse_id')
                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-gray-800 pb-3">Catatan Tambahan</h3>
            <textarea wire:model="notes" rows="4" placeholder="Misal: Untuk kebutuhan darurat Ruang ICU..." class="w-full bg-gray-50 dark:bg-white/[0.03] border-gray-100 dark:border-gray-800 rounded-xl text-sm px-4 py-3 mt-4 focus:ring-brand-500 focus:border-brand-500 transition-all font-medium"></textarea>
        </div>
    </div>

    <!-- Item Selection -->
    <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-white/[0.02] flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Daftar Barang Permintaan</h3>
            <button @click="$dispatch('open-item-modal')" class="flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-black text-white hover:bg-emerald-600 transition-all uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Item
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.02] border-b border-gray-50 dark:border-gray-800">
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase text-center w-32">Qty Diminta</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $index => $item)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.03] transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 dark:text-white block">{{ $item['name'] }}</span>
                                <span class="text-[9px] text-gray-400 dark:text-gray-500 font-bold uppercase italic tracking-widest">{{ $item['code'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" wire:model="items.{{ $index }}.qty" step="1" class="w-full bg-gray-50 dark:bg-white/[0.03] border rounded-lg text-sm px-3 py-2 text-center font-black text-brand-600 focus:ring-2 transition-all shadow-inner
                                    @error('items.'.$index.'.qty') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-100 focus:ring-brand-500 focus:border-brand-500 dark:border-gray-800 @enderror">
                                @error('items.'.$index.'.qty')
                                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="removeItem({{ $index }})" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center italic text-gray-300">Belum ada barang yang dipilih. Klik tombol 'Tambah Item' di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!empty($items))
            <div class="p-6 bg-gray-50/30 dark:bg-white/[0.02] flex justify-end gap-3">
                <button wire:click="save" class="px-8 py-3 bg-brand-500 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-brand-600 transition-all shadow-lg hover:shadow-brand-200">
                    Kirim Permintaan
                </button>
            </div>
        @endif
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
             class="fixed inset-0 bg-slate-900/35 backdrop-blur-[2px] transition-opacity"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-2xl text-left bg-white dark:bg-white/[0.03] rounded-2xl shadow-2xl border border-slate-200 dark:border-gray-800 overflow-hidden transform transition-all align-middle">
                
                <!-- Header -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-white/[0.03] border-b border-slate-100 dark:border-gray-800 flex justify-between items-center text-xs uppercase font-black tracking-widest text-slate-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Pilih Item Permintaan
                    </div>
                    <button @click="open = false" class="text-slate-400 dark:text-gray-500 hover:text-slate-600 dark:hover:text-gray-300 transition-colors bg-white dark:bg-white/[0.03] w-8 h-8 rounded-lg border border-slate-100 dark:border-gray-800 flex items-center justify-center shadow-sm">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-white/[0.03] border border-slate-200 dark:border-gray-800 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 font-bold text-slate-700 dark:text-gray-300 shadow-inner" placeholder="Cari nama barang atau kode...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-gray-500">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    <!-- Results List -->
                    <div class="max-h-[400px] overflow-y-auto rounded-xl border border-slate-100 dark:border-gray-800 divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-white/[0.03] shadow-sm">
                        <!-- Loading State -->
                        <div wire:loading wire:target="search" class="p-16 text-center">
                            <svg class="animate-spin h-8 w-8 text-brand-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <p class="text-[10px] text-slate-400 dark:text-gray-500 mt-4 uppercase font-black tracking-widest italic">Mencari Item...</p>
                        </div>

                        <!-- Results Content -->
                        <div wire:loading.remove wire:target="search">
                            @forelse($searchResults as $item)
                                <button wire:click="addItem({{ $item->id }})" class="w-full flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-white/[0.03] transition-all text-left group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center font-black text-lg border border-indigo-100 dark:border-indigo-500/20 shadow-sm shadow-indigo-50">
                                            {{ substr($item->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white group-hover:text-indigo-700 text-base leading-tight">{{ $item->name }}</div>
                                            <div class="text-[9px] text-slate-400 dark:text-gray-500 uppercase font-black tracking-widest mt-1 flex items-center gap-2">
                                                <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-white/[0.06] rounded text-slate-500 dark:text-gray-400">{{ $item->code }}</span>
                                                <span class="text-indigo-400">•</span>
                                                <span>{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 bg-white dark:bg-white/[0.03] border border-indigo-200 px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all uppercase tracking-widest shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600">
                                        PILIH
                                    </div>
                                </button>
                            @empty
                                <div class="p-16 text-center text-slate-300 dark:text-gray-600">
                                    @if(strlen($search) >= 2)
                                        <svg class="w-12 h-12 mx-auto opacity-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Item tidak ditemukan</p>
                                    @else
                                        <svg class="w-12 h-12 mx-auto opacity-10 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Ketik nama barang...</p>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
