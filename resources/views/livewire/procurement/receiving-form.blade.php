<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
    <!-- Main Form Section -->
    <div class="xl:col-span-3 space-y-6">
        <!-- Items Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-600">Rincian Item & Batch</h3>
                <button @click="$dispatch('open-item-modal')"
                    class="flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-black text-white hover:bg-emerald-600 transition-all uppercase tracking-widest shadow-lg shadow-emerald-500/20">
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
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-12">#</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400">Nama Item</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-24">Qty Terima</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-48">Batch & Expired
                            </th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-32">Harga & Disc
                                (%)</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-20">PPN (%)</th>
                            <th class="px-4 py-3 font-black uppercase tracking-wider text-gray-400 w-32 text-right">
                                Subtotal</th>
                            <th class="px-4 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($rows as $index => $row)
                            <tr wire:key="row-{{ $index }}" class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-4 py-3 text-gray-400 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="font-bold text-gray-900 block">{{ $row['item_name'] ?: 'Pilih Item...' }}</span>
                                    <span
                                        class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ $row['item_code'] }}</span>
                                    @error("rows.$index.item_id") <span
                                        class="text-[10px] text-red-500 font-bold block mt-1">Item wajib dipilih</span>
                                    @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="rows.{{ $index }}.qty_received" type="number"
                                        class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-500 text-center font-bold">
                                    @if($row['qty_remaining'] > 0)
                                        <div class="flex flex-col items-center mt-1">
                                            <span class="text-[9px] text-gray-400 italic">Pesanan:
                                                {{ $row['qty_ordered'] }}</span>
                                            <span class="text-[9px] text-brand-600 font-bold">Sisa:
                                                {{ $row['qty_remaining'] }}</span>
                                        </div>
                                    @endif
                                    @error("rows.$index.qty_received") <span
                                    class="text-[10px] text-red-500 font-bold">Error</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        <input wire:model.live="rows.{{ $index }}.batch_number" type="text"
                                            placeholder="BATCH-XXX"
                                            class="w-full px-2 py-1 bg-white border border-gray-200 rounded text-[10px] font-mono uppercase">
                                        <input wire:model.live="rows.{{ $index }}.expired_date" type="date"
                                            class="w-full px-2 py-1 bg-white border border-gray-200 rounded text-[10px]">
                                    </div>
                                    @error("rows.$index.batch_number") <span
                                    class="text-[9px] text-red-500 font-bold">Wajib</span> @enderror
                                    @error("rows.$index.expired_date") <span
                                    class="text-[9px] text-red-500 font-bold">Format Salah</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        <div class="relative">
                                            <span
                                                class="absolute inset-y-0 left-1 flex items-center text-gray-400 text-[8px] font-black">Rp</span>
                                            <input wire:model.live="rows.{{ $index }}.purchase_price" type="number"
                                                class="w-full pl-5 pr-1 py-1 bg-white border border-gray-200 rounded text-right text-[10px] font-bold">
                                        </div>
                                        <div class="relative">
                                            <span
                                                class="absolute inset-y-0 left-1 flex items-center text-gray-400 text-[8px] font-black">Dic</span>
                                            <input wire:model.live="rows.{{ $index }}.discount_percentage" type="number"
                                                class="w-full pl-5 pr-1 py-1 bg-white border border-gray-200 rounded text-right text-[10px]">
                                        </div>
                                    </div>
                                    @error("rows.$index.purchase_price") <span
                                    class="text-[9px] text-red-500 font-bold">Error</span> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="rows.{{ $index }}.ppn_percentage" type="number"
                                        class="w-full px-1 py-1 bg-white border border-gray-200 rounded text-center text-[10px]">
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="font-bold text-gray-900 block">Rp{{ number_format($row['subtotal']) }}</span>
                                    @if($row['discount_amount'] > 0)
                                        <span class="text-[8px] text-rose-500 font-bold">Disc:
                                            -Rp{{ number_format($row['discount_amount']) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(count($rows) > 1)
                                        <button wire:click="removeRow({{ $index }})"
                                            class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
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

            <div class="p-4 bg-gray-50/30">
                <button wire:click="addRow"
                    class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-black uppercase tracking-widest text-[10px] transition-colors">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Baris Baru
                </button>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black uppercase tracking-wider text-gray-600 mb-4">Catatan Tambahan</h3>
            <textarea wire:model="notes" rows="3"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all placeholder:text-gray-400"
                placeholder="Ketik catatan di sini (misal: barang reject 1, kemasan rusak, dll)..."></textarea>
        </div>
    </div>

    <!-- Sidebar Info Section -->
    <div class="xl:col-span-1 space-y-6">
        <!-- Header Controls -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <h3 class="text-sm font-black uppercase tracking-wider text-gray-600 mb-2">Informasi Header</h3>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Penerimaan</label>
                <input wire:model="receiving_number" type="text"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 cursor-not-allowed"
                    disabled>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Referensi PO
                    (Opsional)</label>
                <select wire:model.live="purchase_order_id"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer font-bold">
                    <option value="">Pilih PO / Emergency</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier->name }} ({{ $po->sp_type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-100 mt-2">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model.live="is_triangulated"
                        class="mt-1 rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                    <div class="flex-1">
                        <span class="text-[10px] font-black uppercase text-amber-700 block tracking-tighter">Verifikasi
                            Triangulasi SOP</span>
                        <span class="text-[9px] text-amber-600 leading-tight block mt-0.5 font-medium">Saya menyatakan
                            fisik barang telah sesuai dengan Faktur dan Surat Pesanan.</span>
                    </div>
                </label>
                @error('is_triangulated') <span class="text-[9px] text-red-500 font-bold block mt-1">SOP: Wajib
                Verifikasi Triangulasi!</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Supplier</label>
                <select wire:model="supplier_id"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer">
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <span class="text-[10px] text-red-500 font-bold block">Wajib dipilih</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Gudang Tujuan</label>
                <select wire:model="warehouse_id"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer">
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id') <span class="text-[10px] text-red-500 font-bold block">Wajib dipilih</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Terima</label>
                    <input wire:model="receiving_date" type="date"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Faktur</label>
                    <input wire:model="invoice_date" type="date"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Faktur
                    Supplier</label>
                <input wire:model="invoice_number" type="text" placeholder="Faktur-12345"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 transition-all outline-none">
                @error('invoice_number') <span class="text-[10px] text-red-500 font-bold block">Wajib diisi</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Upload Foto Faktur</label>
                <div class="relative group">
                    <input type="file" wire:model="invoice_file" class="hidden" id="invoice_file">
                    <label for="invoice_file"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-brand-500 transition-all cursor-pointer">
                        @if($invoice_file)
                            <div class="flex items-center gap-2 text-brand-600">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span class="text-xs font-bold">File Terpilih</span>
                            </div>
                        @elseif($invoice_file_path)
                            <div class="flex flex-col items-center">
                                <span class="text-[8px] font-black text-brand-500 uppercase">Lihat Dokumen</span>
                                <a href="{{ asset('storage/' . $invoice_file_path) }}" target="_blank"
                                    class="text-xs text-brand-600 font-bold hover:underline">Faktur Terunggah</a>
                            </div>
                        @else
                            <svg class="text-gray-400 mb-1" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        @endif
                    </label>
                    <div wire:loading wire:target="invoice_file"
                        class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl">
                        <svg class="animate-spin h-5 w-5 text-brand-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>
                @error('invoice_file') <span class="text-[10px] text-red-500 font-bold block mt-1">{{ $message }}</span>
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
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block w-full max-w-2xl text-left bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all align-middle">

                <!-- Header -->
                <div
                    class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center text-xs uppercase font-black tracking-widest text-slate-500">
                    <div class="flex items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Pilih Item Penerimaan
                    </div>
                    <button @click="open = false"
                        class="text-slate-400 hover:text-slate-600 transition-colors bg-white w-8 h-8 rounded-lg border border-slate-100 flex items-center justify-center shadow-sm">
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
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-slate-700 shadow-inner"
                            placeholder="Cari nama barang atau kode...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="3">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    </div>

                    <!-- Results List -->
                    <div
                        class="max-h-[400px] overflow-y-auto rounded-xl border border-slate-100 divide-y divide-slate-100 bg-white shadow-sm">
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
                            <p class="text-[10px] text-slate-400 mt-4 uppercase font-black tracking-widest italic">
                                Mencari Item...</p>
                        </div>

                        <!-- Results Content -->
                        <div wire:loading.remove wire:target="itemSearch">
                            @forelse($searchResults as $item)
                                <button wire:click="selectItem({{ $item->id }})"
                                    class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition-all text-left group">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-lg border border-emerald-100 shadow-sm shadow-emerald-50">
                                            {{ substr($item->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-bold text-slate-800 group-hover:text-emerald-700 text-base leading-tight">
                                                {{ $item->name }}
                                            </div>
                                            <div
                                                class="text-[9px] text-slate-400 uppercase font-black tracking-widest mt-1 flex items-center gap-2">
                                                <span
                                                    class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-500">{{ $item->code }}</span>
                                                <span class="text-indigo-400">•</span>
                                                <span>{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-[10px] font-black text-emerald-600 bg-white border border-emerald-200 px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all uppercase tracking-widest shadow-sm hover:bg-emerald-600 hover:text-white hover:border-emerald-600">
                                        PILIH
                                    </div>
                                </button>
                            @empty
                                <div class="p-16 text-center text-slate-300">
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