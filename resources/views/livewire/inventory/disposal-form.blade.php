<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-5">Header Berita Acara</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">No. Berita Acara</label>
                        <input type="text" wire:model="disposal_number" readonly class="w-full rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5 text-sm font-mono font-bold text-red-600 dark:border-gray-800 dark:bg-gray-800 dark:text-red-400">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Tipe Transaksi</label>
                        <select wire:model.live="type" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-500 outline-none dark:border-gray-800 dark:bg-gray-900">
                            <option value="disposal text-red-500">Pemusnahan (Disposal)</option>
                            <option value="return_to_supplier text-orange-500">Retur ke Supplier</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Gudang Asal</label>
                        <select wire:model.live="warehouse_id" @if(!empty($rows)) disabled @endif class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-500 outline-none dark:border-gray-800 dark:bg-gray-900 @if(!empty($rows)) opacity-50 @endif">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @if(!empty($rows))
                            <p class="text-[9px] text-gray-400 mt-1 italic">* Gudang tidak bisa diubah jika item sudah dipilih.</p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Tanggal Proses</label>
                        <input type="date" wire:model="disposal_date" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-500 outline-none dark:border-gray-800 dark:bg-gray-900">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Catatan / Alasan Umum</label>
                        <textarea wire:model="notes" rows="3" placeholder="Misal: Pemusnahan rutin triwulan atau retur barang cacat..." class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-500 outline-none dark:border-gray-800 dark:bg-gray-900 font-medium italic"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button wire:click="save('draft')" wire:loading.attr="disabled" class="w-full rounded-xl border border-gray-200 bg-white py-4 text-xs font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                    <span wire:loading.remove wire:target="save('draft')">Simpan Draft</span>
                    <span wire:loading wire:target="save('draft')">Memproses...</span>
                </button>
                <button wire:click="save('posted')" wire:loading.attr="disabled" class="w-full rounded-xl bg-red-600 py-4 text-xs font-black uppercase tracking-widest text-white hover:bg-red-700 shadow-lg shadow-red-600/20">
                    <span wire:loading.remove wire:target="save('posted')">Post & Potong Stok ⚡</span>
                    <span wire:loading wire:target="save('posted')">Posting...</span>
                </button>
            </div>
            <a href="{{ route('inventory.disposals.index') }}" class="block text-center text-xs font-bold text-gray-400 uppercase tracking-tighter hover:text-gray-600 transition-colors">Kembali ke Daftar</a>
        </div>

        <!-- Main Items -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col h-full min-h-[600px]">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/30">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white">Daftar Item Rusak/Expired</h3>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase font-bold tracking-tight">Pilih stok berdasarkan Batch & ED</p>
                    </div>
                    <button @click="$dispatch('open-item-modal')" class="flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-black text-white hover:bg-emerald-600 transition-all uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Cari Item Stok
                    </button>
                </div>

                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-4 w-10">No</th>
                                <th class="px-6 py-4">Item & Batch</th>
                                <th class="px-6 py-4 text-center">Batch / ED</th>
                                <th class="px-6 py-4 text-center w-24">Stok</th>
                                <th class="px-6 py-4 text-center w-28">QTY Keluar</th>
                                <th class="px-6 py-4">Alasan Spesifik</th>
                                <th class="px-6 py-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($rows as $index => $row)
                                <tr class="hover:bg-gray-50/30 dark:hover:bg-white/[0.01]">
                                    <td class="px-6 py-4 text-xs font-bold text-gray-300">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-white uppercase leading-tight">{{ $row['item_name'] }}</span>
                                            <span class="text-[9px] font-mono text-gray-400">{{ $row['item_code'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 capitalize">{{ $row['batch_number'] }}</span>
                                            <span class="text-[9px] font-bold text-red-500 uppercase tracking-tighter">ED: {{ $row['expiry_date'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-xs text-gray-500">
                                        {{ number_format($row['available_qty']) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" step="0.01" wire:model.live="rows.{{ $index }}.qty" class="w-full rounded-lg border border-gray-100 py-1.5 px-2 text-xs font-black text-center focus:border-red-500 outline-none dark:bg-gray-800">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" wire:model="rows.{{ $index }}.reason" placeholder="..." class="w-full bg-transparent border-none p-0 text-xs italic text-gray-500 focus:ring-0">
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="removeRow({{ $index }})" class="text-gray-300 hover:text-red-500"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-32 text-center opacity-30 italic text-sm">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                            <p class="text-xs font-black uppercase tracking-widest text-gray-500">Belum ada item dipilih</p>
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

    <!-- Search & Batch Modal -->
    <div x-data="{ open: @entangle('showItemModal') }" 
         x-show="open" 
         @open-item-modal.window="open = true"
         class="fixed inset-0 z-[1000] overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>

            <div class="relative w-full max-w-3xl rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-900 border border-white/10">
                <div class="flex items-center justify-between mb-8 border-b border-gray-50 dark:border-gray-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-600/30">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tighter text-gray-900 dark:text-white leading-tight">Cari & Pilih Batch Barang</h3>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Hanya menampilkan batch yang tersedia di gudang terpilih</p>
                        </div>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left: Search Results -->
                    <div class="space-y-4">
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="itemSearch" placeholder="Cari nama barang..." class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-4 pr-10 text-sm font-bold focus:bg-white focus:ring-2 focus:ring-red-500/20 outline-none dark:border-gray-800 dark:bg-gray-800 transition-all">
                        </div>
                        <div class="max-h-[350px] overflow-y-auto pr-2 flex flex-col gap-2">
                            @foreach($searchResults as $item)
                                <button wire:click="selectItem({{ $item->id }})" class="w-full flex items-center justify-between p-3 rounded-lg border {{ $selectedItemForBatch?->id == $item->id ? 'border-red-500 bg-red-50/50' : 'border-gray-50 bg-white' }} hover:border-red-500 transition-all text-left group dark:bg-gray-800">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-gray-800 dark:text-white group-hover:text-red-500 uppercase tracking-tight">{{ $item->name }}</span>
                                        <span class="text-[9px] font-mono text-gray-400">{{ $item->code }}</span>
                                    </div>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="{{ $selectedItemForBatch?->id == $item->id ? 'text-red-500' : 'text-gray-200' }}"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right: Batch List -->
                    <div class="bg-gray-50/50 dark:bg-white/[0.02] rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                        <label class="text-[10px] font-black uppercase text-gray-400 mb-3 block">Pilih Batch Tersedia:</label>
                        @if($selectedItemForBatch)
                            <div class="flex flex-col gap-2">
                                @forelse($itemBatches as $batch)
                                    <button wire:click="addBatchRow({{ $batch->id }})" class="flex items-center justify-between p-3 rounded-lg bg-white border border-gray-50 hover:bg-red-50 hover:border-red-300 transition-all text-left dark:bg-gray-800 dark:border-gray-700">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 capitalize">{{ $batch->batch_number }}</span>
                                            <span class="text-[10px] font-bold text-red-500 uppercase">ED: {{ $batch->expiry_date->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] font-black text-gray-900 dark:text-white">Stok: {{ number_format($batch->current_qty) }}</span>
                                            <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest mt-0.5">Pilih ↗</p>
                                        </div>
                                    </button>
                                @empty
                                    <p class="py-12 text-center text-gray-400 text-xs italic">Tidak ada stok tersedia di gudang ini.</p>
                                @endforelse
                            </div>
                        @else
                            <div class="py-20 text-center text-gray-300 opacity-50 italic text-xs">
                                <p><- Silakan pilih item di sebelah kiri</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
