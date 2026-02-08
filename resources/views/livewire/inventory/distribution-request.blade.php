<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.distributions.index') }}" class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-brand-500 hover:border-brand-500 transition-all shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div>
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Buat Permintaan Stok</h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 block">Internal Requisition (LPLPO Internal)</span>
            </div>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-xs font-bold uppercase italic">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Warehouse Selection -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-50 pb-3">Konfigurasi Gudang</h3>
            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1.5 block">Gudang Sumber (Origin)</label>
                <select wire:model="origin_warehouse_id" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-brand-500 focus:border-brand-500 transition-all font-bold text-gray-700">
                    <option value="">Pilih Gudang Sumber</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
                @error('origin_warehouse_id') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">Wajib diisi</span> @enderror
            </div>

            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1.5 block">Unit/Depo Peminta (Destination)</label>
                <select wire:model="destination_warehouse_id" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 focus:ring-brand-500 focus:border-brand-500 transition-all font-bold text-gray-700">
                    <option value="">Pilih Unit Peminta</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
                @error('destination_warehouse_id') <span class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-50 pb-3">Catatan Tambahan</h3>
            <textarea wire:model="notes" rows="4" placeholder="Misal: Untuk kebutuhan darurat Ruang ICU..." class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm px-4 py-3 mt-4 focus:ring-brand-500 focus:border-brand-500 transition-all font-medium"></textarea>
        </div>
    </div>

    <!-- Item Selection -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-gray-50/30">
            <div class="relative">
                <input type="text" wire:model.live="search" placeholder="Cari Nama Barang / Kode untuk ditambahkan..." class="w-full bg-white border-gray-200 rounded-xl text-sm px-10 py-3 focus:ring-brand-500 focus:border-brand-500 transition-all font-medium">
                <div class="absolute left-4 top-3.5 text-gray-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>

                @if(!empty($searchResults))
                    <div class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden">
                        @foreach($searchResults as $result)
                            <button wire:click="addItem({{ $result->id }})" class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors text-left border-b border-gray-50 last:border-0">
                                <div>
                                    <span class="text-sm font-black text-gray-900 block">{{ $result->name }}</span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $result->code }} | {{ $result->category->name }}</span>
                                </div>
                                <span class="p-1 px-3 bg-brand-50 text-brand-500 text-[10px] font-black uppercase rounded-lg italic">Pilih</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-50">
                        <th class="px-6 py-4 font-black text-gray-400 uppercase">Barang</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-32">Qty Diminta</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 block">{{ $item['name'] }}</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase italic tracking-widest">{{ $item['code'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" wire:model="items.{{ $index }}.qty" step="1" class="w-full bg-gray-50 border-gray-100 rounded-lg text-sm px-3 py-2 text-center font-black text-brand-600 focus:ring-brand-500 focus:border-brand-500 transition-all shadow-inner">
                                @error('items.'.$index.'.qty') <span class="text-[8px] text-red-500 font-bold uppercase block mt-1">Invalid</span> @enderror
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="removeItem({{ $index }})" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center italic text-gray-300">Belum ada barang yang dipilih. Cari dan pilih barang di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!empty($items))
            <div class="p-6 bg-gray-50/30 flex justify-end">
                <button wire:click="save" class="px-8 py-3 bg-brand-500 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-brand-600 transition-all shadow-lg hover:shadow-brand-200">
                    Kirim Permintaan
                </button>
            </div>
        @endif
    </div>
</div>
