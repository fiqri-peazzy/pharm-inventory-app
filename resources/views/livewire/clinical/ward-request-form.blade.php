<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm shadow-indigo-50">
                <i class="ph ph-list-plus text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Form Permintaan (Amprahan)</h2>
                <p class="text-sm text-slate-500">Buat permintaan stok baru untuk unit <strong>{{ \App\Models\ServiceUnit::find($service_unit_id)->name ?? '-' }}</strong></p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('clinical.ward-requests.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                Batal
            </a>
            <button wire:click="save" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-lg shadow-indigo-200 transition-all flex items-center gap-2 transform hover:-translate-y-0.5 active:translate-y-0">
                <i class="ph-fill ph-check-circle"></i>
                Kirim Permintaan
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Request Settings -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2 text-sm uppercase tracking-wider">
                    <i class="ph-bold ph-gear text-indigo-500"></i>
                    Detail Permintaan
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Nomor Permintaan</label>
                        <input type="text" wire:model="request_number" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg font-mono text-sm text-slate-600 italic cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Unit Peminta</label>
                        <select wire:model.live="service_unit_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            @foreach($serviceUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Ditujukan Ke (Depo Sumber)</label>
                        <select wire:model.live="warehouse_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            @foreach($pharmacies as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Catatan Tambahan</label>
                        <textarea wire:model="notes" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm h-24" placeholder="Contoh: Stok kritis, butuh segera..."></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 flex gap-3">
                <div class="text-amber-500"><i class="ph-fill ph-info text-2xl"></i></div>
                <div>
                    <h4 class="text-xs font-bold text-amber-900 uppercase">Informasi Amprahan</h4>
                    <p class="text-[10px] text-amber-700 leading-relaxed mt-1">
                        Permintaan yang sudah dikirim akan masuk ke antrian <strong>{{ \App\Models\Warehouse::find($warehouse_id)->name ?? 'Depo' }}</strong> untuk disetujui dan disiapkan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Item List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2 text-sm uppercase tracking-wider">
                        <i class="ph-bold ph-package text-indigo-500"></i>
                        Daftar Barang yang Diminta
                    </h3>
                    <button @click="$dispatch('open-item-modal')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 p-1.5 px-3 rounded-md hover:bg-indigo-50 transition-all border border-indigo-100">
                        <i class="ph ph-plus-circle"></i> Tambah Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 border-b border-slate-100 uppercase tracking-wider">
                                <th class="px-6 py-4">Nama Barang</th>
                                <th class="px-6 py-4 text-center">Jumlah (Qty)</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4 w-10 text-center text-slate-400"><i class="ph ph-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($rows as $index => $row)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800">{{ $row['item_name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 w-32">
                                        <input type="number" wire:model="rows.{{ $index }}.qty_requested" class="w-full px-2 py-1.5 bg-white border border-slate-100 rounded text-center font-bold focus:ring-indigo-500 focus:border-indigo-500 shadow-inner" min="1">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" wire:model="rows.{{ $index }}.notes" class="w-full px-3 py-1.5 bg-white border border-slate-100 rounded text-xs text-slate-500 italic focus:ring-indigo-500 focus:border-indigo-500 shadow-inner" placeholder="Pesan untuk apoteker...">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="removeRow({{ $index }})" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2 opacity-30">
                                            <i class="ph ph-package text-5xl"></i>
                                            <span class="italic text-slate-500">Belum ada item ditambahkan.</span>
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
         class="fixed inset-0 z-[1000000] overflow-y-auto font-sans" 
         style="display: none;">
         
        <!-- Backdrop -->
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center text-sm uppercase font-bold tracking-widest text-slate-500">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-magnifying-glass text-indigo-500"></i>
                        CARI OBAT / ALKES
                    </div>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                        <i class="ph ph-x-circle text-2xl"></i>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="itemSearch" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 shadow-inner" placeholder="Cari nama barang atau kode...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">
                            <i class="ph ph-magnifying-glass font-bold"></i>
                        </div>
                    </div>

                    <div class="max-h-[400px] overflow-y-auto rounded-xl border border-slate-100 divide-y divide-slate-100 bg-white">
                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="itemSearch" class="p-16 text-center">
                            <i class="ph ph-circle-notch ph-spin text-4xl text-indigo-500"></i>
                            <p class="text-xs text-slate-400 mt-4 uppercase font-bold tracking-[0.2em]">Mencari Data...</p>
                        </div>

                        <!-- Search Results -->
                        <div wire:loading.remove wire:target="itemSearch">
                            @forelse($searchResults as $item)
                                <button wire:click="selectItem({{ $item->id }})" class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition-all text-left group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black text-lg border border-indigo-100 shadow-sm shadow-indigo-50">
                                            {{ substr($item->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 group-hover:text-indigo-700 text-base">{{ $item->name }}</div>
                                            <div class="text-[10px] text-slate-500 uppercase font-black tracking-widest mt-1">
                                                {{ $item->code }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-[10px] font-black text-indigo-600 bg-white border border-indigo-200 px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all uppercase tracking-[0.1em] shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600">
                                        TAMBAHKAN
                                    </div>
                                </button>
                            @empty
                                <div class="p-16 text-center text-slate-300">
                                    @if(strlen($itemSearch) >= 2)
                                        <i class="ph ph-warning-circle text-5xl opacity-20"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest mt-4">Barang tidak ditemukan</p>
                                    @else
                                        <i class="ph ph-magnifying-glass text-5xl opacity-20"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest mt-4">Ketik nama barang untuk amprahan</p>
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
