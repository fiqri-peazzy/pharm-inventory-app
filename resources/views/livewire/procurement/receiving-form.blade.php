<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
    <!-- Main Form Section -->
    <div class="xl:col-span-3 space-y-6">
        <!-- Items Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-600">Rincian Item & Batch</h3>
                <div class="relative w-72">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="itemSearch" type="text" placeholder="Tambah item manual..." class="block w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg bg-white text-xs focus:ring-2 focus:ring-brand-500 transition-all">
                    
                    @if(!empty($searchResults))
                        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                            @foreach($searchResults as $item)
                                <button wire:click="selectItem({{ $item->id }})" class="w-full text-left px-4 py-2 text-sm hover:bg-brand-50 transition-colors border-b border-gray-50 last:border-0">
                                    <span class="font-bold text-gray-900 block">{{ $item->name }}</span>
                                    <span class="text-[10px] text-gray-400 uppercase font-black">{{ $item->code }} | {{ $item->category->name ?? '-' }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-12">#</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400">Nama Item</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-24">Qty Terima</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-32">Nomor Batch</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-36">Expired Date</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-32">Harga Satuan</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-32 text-right">Subtotal</th>
                            <th class="px-4 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($rows as $index => $row)
                            <tr wire:key="row-{{ $index }}" class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-4 py-3 text-gray-400 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-900 block">{{ $row['item_name'] ?: 'Pilih Item...' }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ $row['item_code'] }}</span>
                                    @error("rows.$index.item_id") <span class="text-[10px] text-red-500 font-bold block mt-1">Item wajib dipilih</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="rows.{{ $index }}.qty_received" type="number" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-500 text-center font-bold">
                                    @if($row['qty_ordered'] > 0)
                                        <span class="text-[9px] text-gray-400 block mt-1 text-center italic">Order: {{ $row['qty_ordered'] }}</span>
                                    @endif
                                    @error("rows.$index.qty_received") <span class="text-[10px] text-red-500 font-bold">Error</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="rows.{{ $index }}.batch_number" type="text" placeholder="BATCH-XXX" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-500 font-mono text-center uppercase tracking-tighter">
                                    @error("rows.$index.batch_number") <span class="text-[10px] text-red-500 font-bold">Wajib</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="rows.{{ $index }}.expired_date" type="date" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-500">
                                    @error("rows.$index.expired_date") <span class="text-[10px] text-red-500 font-bold">Format Salah</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-2 flex items-center text-gray-400 text-[10px] font-black">Rp</span>
                                        <input wire:model.live="rows.{{ $index }}.purchase_price" type="number" class="w-full pl-7 pr-2 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-500 text-right">
                                    </div>
                                    @error("rows.$index.purchase_price") <span class="text-[10px] text-red-500 font-bold">Error</span> @enderror
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-bold text-gray-900 block">Rp{{ number_format($row['subtotal']) }}</span>
                                    <span class="text-[9px] text-gray-400">PPN {{ (float)$row['ppn_percentage'] }}%</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if(count($rows) > 1)
                                        <button wire:click="removeRow({{ $index }})" class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-gray-50/30">
                <button wire:click="addRow" class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-black uppercase tracking-widest text-[10px] transition-colors">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Tambah Baris Baru
                </button>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black uppercase tracking-wider text-gray-600 mb-4">Catatan Tambahan</h3>
            <textarea wire:model="notes" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all placeholder:text-gray-400" placeholder="Ketik catatan di sini (misal: barang reject 1, kemasan rusak, dll)..."></textarea>
        </div>
    </div>

    <!-- Sidebar Info Section -->
    <div class="xl:col-span-1 space-y-6">
        <!-- Header Controls -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <h3 class="text-sm font-black uppercase tracking-wider text-gray-600 mb-2">Informasi Header</h3>
            
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Penerimaan</label>
                <input wire:model="receiving_number" type="text" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 cursor-not-allowed" disabled>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Referensi PO (Opsional)</label>
                <select wire:model.live="purchase_order_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer">
                    <option value="">Pilih PO / Emergency</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Supplier</label>
                <select wire:model="supplier_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer">
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <span class="text-[10px] text-red-500 font-bold block">Wajib dipilih</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Gudang Tujuan</label>
                <select wire:model="warehouse_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer">
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id') <span class="text-[10px] text-red-500 font-bold block">Wajib dipilih</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Terima</label>
                    <input wire:model="receiving_date" type="date" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Faktur</label>
                    <input wire:model="invoice_date" type="date" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Faktur Supplier</label>
                <input wire:model="invoice_number" type="text" placeholder="Faktur-12345" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all outline-none">
                @error('invoice_number') <span class="text-[10px] text-red-500 font-bold block">Wajib diisi</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Upload Foto Faktur</label>
                <div class="relative group">
                    <input type="file" wire:model="invoice_file" class="hidden" id="invoice_file">
                    <label for="invoice_file" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-brand-500 transition-all cursor-pointer">
                        @if($invoice_file)
                            <div class="flex items-center gap-2 text-brand-600">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                <span class="text-xs font-bold">File Terpilih</span>
                            </div>
                        @elseif($invoice_file_path)
                            <div class="flex flex-col items-center">
                                <span class="text-[8px] font-black text-brand-500 uppercase">Lihat Dokumen</span>
                                <a href="{{ asset('storage/' . $invoice_file_path) }}" target="_blank" class="text-xs text-brand-600 font-bold hover:underline">Faktur Terunggah</a>
                            </div>
                        @else
                            <svg class="text-gray-400 mb-1" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        @endif
                    </label>
                    <div wire:loading wire:target="invoice_file" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl">
                        <svg class="animate-spin h-5 w-5 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
                @error('invoice_file') <span class="text-[10px] text-red-500 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Summary & Totals -->
        <div class="bg-brand-500 rounded-2xl p-6 shadow-xl shadow-brand-100 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-brand-100">Ringkasan Biaya</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-brand-200 font-medium">Subtotal (Gross)</span>
                    <span class="text-white font-bold">Rp{{ number_format($total_amount) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-brand-200 font-medium">Total Pajak (PPN)</span>
                    <span class="text-white font-bold">Rp{{ number_format($ppn_amount) }}</span>
                </div>
                <div class="pt-3 border-t border-brand-400 flex justify-between items-center">
                    <span class="text-white text-xs font-black uppercase tracking-widest">Grand Total</span>
                    <span class="text-white text-xl font-black">Rp{{ number_format($grand_total) }}</span>
                </div>
            </div>

            <div class="pt-4 grid grid-cols-1 gap-2">
                @if($status === 'draft')
                    <button wire:click="save('posted')" wire:loading.attr="disabled" class="w-full bg-white text-brand-600 text-sm font-black uppercase tracking-widest py-3 rounded-xl hover:bg-brand-50 shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg wire:loading.remove width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"></path></svg>
                        <svg wire:loading class="animate-spin h-4 w-4 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Posting Stok
                    </button>
                    <button wire:click="save('draft')" wire:loading.attr="disabled" class="w-full bg-brand-600 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded-xl hover:bg-brand-700 transition-all">
                        Simpan Draft
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
