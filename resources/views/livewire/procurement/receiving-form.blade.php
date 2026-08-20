<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
    <!-- Main Form Section -->
    <div class="xl:col-span-3 space-y-6">
        <!-- Items Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30 dark:bg-white/[0.02] dark:border-gray-800">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-600 dark:text-gray-300">Rincian Item & Batch</h3>
                <button @click="$dispatch('open-item-modal')"
                    class="flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-black text-white hover:bg-emerald-600 transition-all uppercase tracking-widest shadow-lg shadow-emerald-500/20 dark:shadow-none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Item
                </button>
            </div>

            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 dark:bg-white/[0.02] dark:border-gray-800">
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-12 dark:text-gray-500">#</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">Nama Item</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-24 dark:text-gray-500">Qty Terima</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-48 dark:text-gray-500">Batch & Expired
                            </th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-32 dark:text-gray-500">Harga & Disc
                                (%)</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-20 dark:text-gray-500">PPN (%)</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-32 text-right dark:text-gray-500">
                                Subtotal</th>
                            <th class="px-4 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($rows as $index => $row)
                            <tr wire:key="row-{{ $index }}" class="hover:bg-gray-50/30 transition-colors dark:hover:bg-white/[0.02]">
                                <td class="px-4 py-3 text-gray-400 font-bold dark:text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="font-bold text-gray-900 block dark:text-white">{{ $row['item_name'] ?: 'Pilih Item...' }}</span>
                                    <span
                                        class="text-[10px] text-gray-400 font-medium uppercase tracking-widest dark:text-gray-500">{{ $row['item_code'] }}</span>
                                    @error("rows.$index.item_id")
                                        <p class="mt-1 flex items-center gap-1 text-[10px] text-red-600 dark:text-red-400">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            Item wajib dipilih
                                        </p>
                                    @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="rows.{{ $index }}.qty_received" type="number"
                                        class="w-full px-2 py-1.5 border rounded-lg focus:ring-1 text-center font-bold dark:bg-white/[0.03] dark:text-white
                                        @error("rows.$index.qty_received") border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                                    @if($row['qty_remaining'] > 0)
                                        <div class="flex flex-col items-center mt-1">
                                            <span class="text-[9px] text-gray-400 italic dark:text-gray-500">Pesanan:
                                                {{ $row['qty_ordered'] }}</span>
                                            <span class="text-[9px] text-brand-600 font-bold dark:text-brand-400">Sisa:
                                                {{ $row['qty_remaining'] }}</span>
                                        </div>
                                    @endif
                                    @error("rows.$index.qty_received")
                                        <p class="mt-1 flex items-center justify-center gap-1 text-[10px] text-red-600 dark:text-red-400">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            Error
                                        </p>
                                    @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        <input wire:model.live="rows.{{ $index }}.batch_number" type="text"
                                            placeholder="BATCH-XXX"
                                            class="w-full px-2 py-1 bg-white border rounded text-[10px] font-mono uppercase dark:bg-white/[0.03] dark:text-white
                                            @error("rows.$index.batch_number") border-red-500 dark:border-red-500 @else border-gray-200 dark:border-gray-800 @enderror">
                                        <input wire:model.live="rows.{{ $index }}.expired_date" type="date"
                                            class="w-full px-2 py-1 bg-white border rounded text-[10px] dark:bg-white/[0.03] dark:text-white
                                            @error("rows.$index.expired_date") border-red-500 dark:border-red-500 @else border-gray-200 dark:border-gray-800 @enderror">
                                    </div>
                                    @error("rows.$index.batch_number")
                                        <p class="mt-1 flex items-center gap-1 text-[9px] text-red-600 dark:text-red-400">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            Wajib
                                        </p>
                                    @enderror
                                    @error("rows.$index.expired_date")
                                        <p class="mt-1 flex items-center gap-1 text-[9px] text-red-600 dark:text-red-400">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            Format Salah
                                        </p>
                                    @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        <div class="relative">
                                            <span
                                                class="absolute inset-y-0 left-1 flex items-center text-gray-400 text-[8px] font-black dark:text-gray-500">Rp</span>
                                            <input wire:model.live="rows.{{ $index }}.purchase_price" type="number"
                                                class="w-full pl-5 pr-1 py-1 bg-white border rounded text-right text-[10px] font-bold dark:bg-white/[0.03] dark:text-white
                                                @error("rows.$index.purchase_price") border-red-500 dark:border-red-500 @else border-gray-200 dark:border-gray-800 @enderror">
                                        </div>
                                        <div class="relative">
                                            <span
                                                class="absolute inset-y-0 left-1 flex items-center text-gray-400 text-[8px] font-black dark:text-gray-500">Dic</span>
                                            <input wire:model.live="rows.{{ $index }}.discount_percentage" type="number"
                                                class="w-full pl-5 pr-1 py-1 bg-white border border-gray-200 rounded text-right text-[10px] dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                                        </div>
                                    </div>
                                    @error("rows.$index.purchase_price")
                                        <p class="mt-1 flex items-center gap-1 text-[9px] text-red-600 dark:text-red-400">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            Error
                                        </p>
                                    @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="rows.{{ $index }}.ppn_percentage" type="number"
                                        class="w-full px-1 py-1 bg-white border border-gray-200 rounded text-center text-[10px] dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="font-bold text-gray-900 block dark:text-white">Rp{{ number_format($row['subtotal']) }}</span>
                                    @if($row['discount_amount'] > 0)
                                        <span class="text-[8px] text-rose-500 font-bold dark:text-rose-400">Disc:
                                            -Rp{{ number_format($row['discount_amount']) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(count($rows) > 1)
                                        <button wire:click="removeRow({{ $index }})"
                                            class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all dark:text-gray-600 dark:hover:bg-red-500/15">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50/30 dark:bg-white/[0.02]">
                <button wire:click="addRow"
                    class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-black uppercase tracking-widest text-[10px] transition-colors dark:text-brand-400 dark:hover:text-brand-300">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Baris Baru
                </button>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800">
            <h3 class="text-sm font-black uppercase tracking-wider text-gray-600 mb-4 dark:text-gray-300">Catatan Tambahan</h3>
            <textarea wire:model="notes" rows="3"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all placeholder:text-gray-400 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white dark:placeholder:text-gray-500"
                placeholder="Ketik catatan di sini (misal: barang reject 1, kemasan rusak, dll)..."></textarea>
        </div>
    </div>

    <!-- Sidebar Info Section -->
    <div class="xl:col-span-1 space-y-6">
        <!-- Header Controls -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4 dark:bg-white/[0.03] dark:border-gray-800">
            <h3 class="text-sm font-black uppercase tracking-wider text-gray-600 mb-2 dark:text-gray-300">Informasi Header</h3>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Nomor Penerimaan</label>
                <input wire:model="receiving_number" type="text"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 cursor-not-allowed dark:bg-white/[0.02] dark:border-gray-800 dark:text-gray-400"
                    disabled>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Referensi PO
                    (Opsional)</label>
                <select wire:model.live="purchase_order_id"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer font-bold dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                    <option value="">Pilih PO / Emergency</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier->name }} ({{ $po->sp_type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-100 mt-2 dark:bg-amber-500/10 dark:border-amber-500/20">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model.live="is_triangulated"
                        class="mt-1 rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                    <div class="flex-1">
                        <span class="text-[10px] font-black uppercase text-amber-700 block tracking-tighter dark:text-amber-400">Verifikasi
                            Triangulasi SOP</span>
                        <span class="text-[9px] text-amber-600 leading-tight block mt-0.5 font-medium dark:text-amber-400/80">Saya menyatakan
                            fisik barang telah sesuai dengan Faktur dan Surat Pesanan.</span>
                    </div>
                </label>
                @error('is_triangulated')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[9px] text-red-600 dark:text-red-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        SOP: Wajib Verifikasi Triangulasi!
                    </p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Supplier</label>
                <select wire:model="supplier_id"
                    class="w-full px-4 py-2 bg-gray-50 border rounded-xl text-sm focus:ring-2 transition-all cursor-pointer dark:bg-white/[0.03] dark:text-white
                    @error('supplier_id') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[10px] text-red-600 dark:text-red-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        Wajib dipilih
                    </p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Gudang Tujuan</label>
                <select wire:model="warehouse_id"
                    class="w-full px-4 py-2 bg-gray-50 border rounded-xl text-sm focus:ring-2 transition-all cursor-pointer dark:bg-white/[0.03] dark:text-white
                    @error('warehouse_id') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[10px] text-red-600 dark:text-red-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        Wajib dipilih
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Tgl Terima</label>
                    <input wire:model="receiving_date" type="date"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Tgl Faktur</label>
                    <input wire:model="invoice_date" type="date"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Nomor Faktur
                    Supplier</label>
                <input wire:model="invoice_number" type="text" placeholder="Faktur-12345"
                    class="w-full px-4 py-2 bg-gray-50 border rounded-xl text-sm focus:ring-2 transition-all outline-none dark:bg-white/[0.03] dark:text-white
                    @error('invoice_number') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                @error('invoice_number')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[10px] text-red-600 dark:text-red-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        Wajib diisi
                    </p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Upload Foto Faktur</label>
                <div class="relative group">
                    <input type="file" wire:model="invoice_file" class="hidden" id="invoice_file">
                    <label for="invoice_file"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-brand-500 transition-all cursor-pointer dark:border-gray-800 dark:bg-white/[0.03] dark:hover:bg-white/[0.05]">
                        @if($invoice_file)
                            <div class="flex items-center gap-2 text-brand-600 dark:text-brand-400">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span class="text-xs font-bold">File Terpilih</span>
                            </div>
                        @elseif($invoice_file_path)
                            <div class="flex flex-col items-center">
                                <span class="text-[8px] font-black text-brand-500 uppercase dark:text-brand-400">Lihat Dokumen</span>
                                <a href="{{ asset('storage/' . $invoice_file_path) }}" target="_blank"
                                    class="text-xs text-brand-600 font-bold hover:underline dark:text-brand-400">Faktur Terunggah</a>
                            </div>
                        @else
                            <svg class="text-gray-400 mb-1 dark:text-gray-500" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        @endif
                    </label>
                    <div wire:loading wire:target="invoice_file"
                        class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl dark:bg-gray-900/80">
                        <svg class="animate-spin h-5 w-5 text-brand-600 dark:text-brand-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>
                @error('invoice_file')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[10px] text-red-600 dark:text-red-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ $message }}
                    </p>
                @enderror
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
                    <button wire:click="save('posted')" wire:loading.attr="disabled"
                        class="w-full bg-white text-brand-600 text-sm font-black uppercase tracking-widest py-3 rounded-xl hover:bg-brand-50 shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg wire:loading.remove width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
                        <svg wire:loading class="animate-spin h-4 w-4 text-brand-600" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Posting Stok
                    </button>
                    <button wire:click="save('draft')" wire:loading.attr="disabled"
                        class="w-full bg-brand-600 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded-xl hover:bg-brand-700 transition-all">
                        Simpan Draft
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Item Search Modal -->
    <div x-data="{ open: false }" @open-item-modal.window="open = true" @close-item-modal.window="open = false"
        x-show="open" class="fixed inset-0 z-[1000000] overflow-y-auto font-sans" style="display: none;">

        <!-- Backdrop -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
            class="fixed inset-0 bg-slate-900/35 transition-opacity"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block w-full max-w-2xl text-left bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all align-middle dark:bg-gray-900 dark:border-gray-800">

                <!-- Header -->
                <div
                    class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center text-xs uppercase font-black tracking-widest text-slate-500 dark:bg-white/[0.02] dark:border-gray-800 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Pilih Item Penerimaan
                    </div>
                    <button @click="open = false"
                        class="text-slate-400 hover:text-slate-600 transition-colors bg-white w-8 h-8 rounded-lg border border-slate-100 flex items-center justify-center shadow-sm dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-500 dark:hover:text-gray-300">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M18 6L6 18M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="itemSearch"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-slate-700 shadow-inner dark:bg-white/[0.03] dark:border-gray-800 dark:text-white"
                            placeholder="Cari nama barang atau kode...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-gray-500">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="3">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    </div>

                    <!-- Results List -->
                    <div
                        class="max-h-[400px] overflow-y-auto rounded-xl border border-slate-100 divide-y divide-slate-100 bg-white shadow-sm dark:bg-white/[0.02] dark:border-gray-800 dark:divide-gray-800">
                        <!-- Loading State -->
                        <div wire:loading wire:target="itemSearch" class="p-16 text-center">
                            <svg class="animate-spin h-8 w-8 text-emerald-500 mx-auto"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-[10px] text-slate-400 mt-4 uppercase font-black tracking-widest italic dark:text-gray-500">
                                Mencari Item...</p>
                        </div>

                        <!-- Results Content -->
                        <div wire:loading.remove wire:target="itemSearch">
                            @forelse($searchResults as $item)
                                <button wire:click="selectItem({{ $item->id }})"
                                    class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition-all text-left group dark:hover:bg-white/[0.03]">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-lg border border-emerald-100 shadow-sm shadow-emerald-50 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20 dark:shadow-none">
                                            {{ substr($item->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-bold text-slate-800 group-hover:text-emerald-700 text-base leading-tight dark:text-white dark:group-hover:text-emerald-400">
                                                {{ $item->name }}
                                            </div>
                                            <div
                                                class="text-[9px] text-slate-400 uppercase font-black tracking-widest mt-1 flex items-center gap-2 dark:text-gray-500">
                                                <span
                                                    class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-500 dark:bg-white/[0.05] dark:text-gray-400">{{ $item->code }}</span>
                                                <span class="text-indigo-400">•</span>
                                                <span>{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-[10px] font-black text-emerald-600 bg-white border border-emerald-200 px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all uppercase tracking-widest shadow-sm hover:bg-emerald-600 hover:text-white hover:border-emerald-600 dark:bg-white/[0.03] dark:border-emerald-500/20 dark:text-emerald-400">
                                        PILIH
                                    </div>
                                </button>
                            @empty
                                <div class="p-16 text-center text-slate-300 dark:text-gray-700">
                                    @if(strlen($itemSearch) >= 2)
                                        <svg class="w-12 h-12 mx-auto opacity-20 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Item tidak ditemukan</p>
                                    @else
                                        <svg class="w-12 h-12 mx-auto opacity-10 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
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