<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar: Header Info -->
        <div class="lg:col-span-1 space-y-6">
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-5 flex items-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg>
                    Informasi Pesanan
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Nomor
                            PO</label>
                        <input type="text" wire:model="po_number" readonly
                            class="w-full rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5 text-sm font-mono font-bold text-brand-600 dark:border-gray-800 dark:bg-gray-800 dark:text-brand-400">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Ambil
                            dari PR (Internal)</label>
                        <select wire:model.live="purchase_request_id"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900 font-bold">
                            <option value="">-- Manual / Tanpa PR --</option>
                            @foreach($approvedPRs as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->request_number }} ({{ $pr->warehouse->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Tipe
                            Surat Pesanan (SP)</label>
                        <select wire:model.live="sp_type"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900 font-bold {{ $sp_type !== 'reguler' ? 'text-red-600 bg-red-50' : 'text-gray-700' }}">
                            <option value="reguler">SP REGULER</option>
                            <option value="narkotika">SP NARKOTIKA</option>
                            <option value="psikotropika">SP PSIKOTROPIKA</option>
                        </select>
                        @if($sp_type !== 'reguler')
                            <span class="text-[9px] text-red-500 mt-1 block font-bold italic tracking-tighter">* Membutuhkan
                                format khusus & SIPA Apoteker</span>
                        @endif
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Supplier
                            Tujuan</label>
                        <select wire:model.live="supplier_id"
                            class="w-full rounded-lg border bg-white px-4 py-2.5 text-sm outline-none dark:bg-gray-900
                            @error('supplier_id') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-brand-500 dark:border-gray-800 @enderror">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Kirim
                            ke Gudang</label>
                        <select wire:model="warehouse_id"
                            class="w-full rounded-lg border bg-white px-4 py-2.5 text-sm outline-none dark:bg-gray-900
                            @error('warehouse_id') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-brand-500 dark:border-gray-800 @enderror">
                            <option value="">-- Pilih Gudang --</option>
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

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Tgl
                                Pesanan</label>
                            <input type="date" wire:model="po_date"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900">
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Estimasi
                                Tiba</label>
                            <input type="date" wire:model="expected_delivery_date"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900">
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-[10px] font-black tracking-widest uppercase text-gray-500">Termin
                            (Hari)</label>
                        <input type="number" wire:model="payment_term"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm focus:border-brand-500 outline-none dark:border-gray-800 dark:bg-gray-900 font-bold text-indigo-600">
                    </div>
                </div>
            </div>

            <div class="bg-indigo-600 rounded-2xl p-6 shadow-xl shadow-indigo-600/20 text-white">
                <div class="flex flex-col gap-2 mb-4">
                    <div class="flex justify-between text-[10px] font-black uppercase opacity-60"><span>Subtotal
                            Netto</span><span>Rp{{ number_format($total_amount - $total_discount) }}</span></div>
                    <div class="flex justify-between text-[10px] font-black uppercase opacity-60"><span>Akumulasi
                            PPN</span><span>Rp{{ number_format($total_ppn) }}</span></div>
                </div>
                <div class="border-t border-white/20 pt-4">
                    <label
                        class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-1 block text-right">Total
                        Pembayaran</label>
                    <div class="text-2xl font-black text-right tracking-tighter">Rp{{ number_format($grand_total) }}
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button wire:click="save('draft')" wire:loading.attr="disabled"
                    class="w-full rounded-xl border border-gray-200 bg-white py-4 text-xs font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                    <span wire:loading.remove wire:target="save('draft')">Simpan Draft</span>
                    <span wire:loading wire:target="save('draft')">Memproses...</span>
                </button>
                <button wire:click="save('submitted')" wire:loading.attr="disabled"
                    class="w-full rounded-xl bg-brand-500 py-4 text-xs font-black uppercase tracking-widest text-white hover:bg-brand-600 shadow-lg shadow-brand-500/20">
                    <span wire:loading.remove wire:target="save('submitted')">Submit Pesanan</span>
                    <span wire:loading wire:target="save('submitted')">Mengirim...</span>
                </button>
            </div>
            <a href="{{ route('procurement.orders.index') }}"
                class="block text-center text-xs font-bold text-gray-400 uppercase tracking-tighter hover:text-gray-600 transition-colors">Batal</a>
        </div>

        <!-- Main Content: Items Table -->
        <div class="lg:col-span-3">
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col h-full min-h-[700px]">
                <div
                    class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/30">
                    <div>
                        <h3
                            class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white flex items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                </path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            Daftar Detail Barang Pesanan
                        </h3>
                    </div>
                    <button @click="$dispatch('open-item-modal')"
                        class="flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-black text-white hover:bg-emerald-600 transition-all uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        Item Manual
                    </button>
                </div>

                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead
                            class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-4 w-10">No</th>
                                <th class="px-6 py-4">Nama Barang / Item Pesanan</th>
                                <th class="px-6 py-4 text-center w-24">QTY</th>
                                <th class="px-6 py-4 text-center w-40">Hrg Satuan (HNA)</th>
                                <th class="px-6 py-4 text-center w-24">Disc%</th>
                                <th class="px-6 py-4 text-center w-24">PPN%</th>
                                <th class="px-6 py-4 text-right">Subtotal</th>
                                <th class="px-6 py-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($rows as $index => $row)
                                <tr class="hover:bg-gray-50/30 dark:hover:bg-white/[0.01] transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-gray-300">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-gray-800 dark:text-white uppercase leading-tight">{{ $row['item_name'] }}</span>
                                            <span
                                                class="text-[10px] font-mono text-indigo-500 uppercase tracking-tighter">{{ $row['item_code'] }}</span>
                                            <input type="text" wire:model="rows.{{ $index }}.notes"
                                                placeholder="Catatan item..."
                                                class="text-[9px] mt-1 italic text-gray-500 bg-transparent border-none focus:ring-0 p-0 w-full placeholder:opacity-30">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" step="0.01"
                                            wire:model.live.debounce.500ms="rows.{{ $index }}.qty_ordered"
                                            class="w-20 rounded-lg border border-gray-100 py-2 px-2 text-xs font-black text-center focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 outline-none">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="relative">
                                            <span
                                                class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400">Rp</span>
                                            <input type="number"
                                                wire:model.live.debounce.500ms="rows.{{ $index }}.purchase_price"
                                                class="w-full rounded-lg border border-gray-100 py-2 pl-7 pr-2 text-xs font-black text-right focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 outline-none text-brand-600">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number"
                                            wire:model.live.debounce.500ms="rows.{{ $index }}.discount_percentage"
                                            class="w-16 rounded-lg border border-gray-100 py-2 px-2 text-xs font-bold text-center focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 outline-none text-red-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number"
                                            wire:model.live.debounce.500ms="rows.{{ $index }}.ppn_percentage"
                                            class="w-16 rounded-lg border border-gray-100 py-2 px-2 text-xs font-bold text-center focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 outline-none text-indigo-500">
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs font-black text-gray-900 dark:text-white">Rp{{ number_format($row['subtotal']) }}</span>
                                            @if($row['discount_amount'] > 0)
                                                <span
                                                    class="text-[9px] text-red-500 font-bold">-Rp{{ number_format($row['discount_amount']) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="removeRow({{ $index }})"
                                            class="p-2 text-gray-300 hover:text-red-500 transition-colors shadow-none"><svg
                                                width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M18 6L6 18M6 6l12 12"></path>
                                            </svg></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-32 text-center opacity-30 italic">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </div>
                                            <p class="text-xs font-black uppercase tracking-widest text-gray-500">Tabel Item
                                                Kosong</p>
                                            <p class="text-[10px] uppercase font-bold text-gray-400 mt-1">Pilih PR Approved
                                                di sidebar atau klik tombol "Item Manual"</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-8 border-t border-gray-100 dark:border-gray-800 bg-gray-50/30">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Informasi
                        Tambahan / Syarat Khusus:</label>
                    <textarea wire:model="notes" rows="2"
                        placeholder="Tuliskan instruksi pengiriman, syarat pembayaran khusus, atau catatan lainnya untuk ditampilan di PO PDF..."
                        class="w-full rounded-xl border border-gray-100 bg-white p-4 text-sm outline-none focus:border-brand-500 dark:bg-gray-800 dark:border-gray-700 transition-all font-medium italic text-gray-600 dark:text-gray-400"></textarea>
                </div>
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
                class="relative inline-block w-full max-w-2xl text-left bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-gray-800 overflow-hidden transform transition-all align-middle">

                <!-- Header -->
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-white/[0.02] border-b border-slate-100 dark:border-gray-800 flex justify-between items-center text-xs uppercase font-black tracking-widest text-slate-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Tambah Item Pesanan Manual
                    </div>
                    <button @click="open = false"
                        class="text-slate-400 hover:text-slate-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors bg-white dark:bg-gray-800 w-8 h-8 rounded-lg border border-slate-100 dark:border-gray-700 flex items-center justify-center shadow-sm">
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
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 font-bold text-slate-700 dark:text-white shadow-inner"
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
                        class="max-h-[400px] overflow-y-auto rounded-xl border border-slate-100 dark:border-gray-800 divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                        <!-- Loading State -->
                        <div wire:loading wire:target="itemSearch" class="p-16 text-center">
                            <svg class="animate-spin h-8 w-8 text-brand-500 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-[10px] text-slate-400 dark:text-gray-500 mt-4 uppercase font-black tracking-widest italic">
                                Mencari Data...</p>
                        </div>

                        <!-- Results Content -->
                        <div wire:loading.remove wire:target="itemSearch">
                            @forelse($searchResults as $item)
                                <button wire:click="addItem({{ $item->id }})"
                                    class="w-full flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-gray-800/50 transition-all text-left group">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center font-black text-lg border border-emerald-100 dark:border-emerald-800 shadow-sm shadow-emerald-50 dark:shadow-none">
                                            {{ substr($item->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-bold text-slate-800 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400 text-base leading-tight">
                                                {{ $item->name }}</div>
                                            <div
                                                class="text-[9px] text-slate-400 dark:text-gray-500 uppercase font-black tracking-widest mt-1 flex items-center gap-2">
                                                <span
                                                    class="px-1.5 py-0.5 bg-slate-100 dark:bg-gray-800 rounded text-slate-500 dark:text-gray-400">{{ $item->code }}</span>
                                                <span class="text-indigo-400">•</span>
                                                <span>{{ $item->unit?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all uppercase tracking-widest shadow-sm hover:bg-emerald-600 hover:text-white hover:border-emerald-600">
                                        TAMBAHKAN
                                    </div>
                                </button>
                            @empty
                                <div class="p-16 text-center text-slate-300 dark:text-gray-600">
                                    @if(strlen($itemSearch) >= 2)
                                        <svg class="w-12 h-12 mx-auto opacity-20 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Barang tidak ditemukan</p>
                                    @else
                                        <svg class="w-12 h-12 mx-auto opacity-10 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Ketik minimal 2 karakter...
                                        </p>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-white/[0.02] border-t border-slate-100 dark:border-gray-800 text-[9px] text-slate-400 dark:text-gray-500 font-bold uppercase tracking-widest italic">
                    * Menampilkan data master barang aktif
                </div>
            </div>
        </div>
    </div>
</div>