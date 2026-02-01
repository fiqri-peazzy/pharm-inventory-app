<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pesanan Barang (PO)</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola pemesanan barang ke supplier berdasarkan PR yang telah disetujui.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat PO Baru
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input wire:model.live="search" type="text" placeholder="Cari nomor PO..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-4 font-semibold">No. Pesanan</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Supplier</th>
                        <th class="px-6 py-4 font-semibold text-right">Total (Inc. PPN)</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($purchaseOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $order->po_number }}
                            </td>
                            <td class="px-6 py-4">
                                {{ date('d/m/Y', strtotime($order->po_date)) }}
                            </td>
                            <td class="px-6 py-4 font-medium">
                                {{ $order->supplier->name }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-600 dark:text-indigo-400">
                                Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Detail</button>
                                <button class="bg-indigo-600 text-white px-3 py-1 rounded text-xs hover:bg-indigo-700 transition-colors">Cetak PO</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada pesanan barang</p>
                                    <p class="text-sm">Klik 'Buat PO Baru' untuk membuat pesanan ke supplier.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($purchaseOrders->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>

    <!-- PO Form Modal -->
    @if($isOpen)
    <div class="fixed inset-0 z-[999] overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-[90%] sm:w-full border border-gray-200 dark:border-gray-700">
                <form wire:submit.prevent="store">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-900 text-white">
                        <div>
                            <h3 class="text-lg font-bold">Form Pembuatan Pesanan (PO)</h3>
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mt-0.5">Automated Procurement Workflow</p>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 overflow-y-auto max-h-[70vh]">
                        <!-- Header PO Details -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-8 bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-dashed border-gray-200 dark:border-gray-600">
                            <div class="md:col-span-3 space-y-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-tighter">No. PO (Auto)</label>
                                <input type="text" wire:model="po_number" readonly class="w-full bg-transparent border-none p-0 text-lg font-mono font-bold text-indigo-600 focus:ring-0">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Pilih Approved PR</label>
                                <select wire:model="purchase_request_id" wire:change="loadFromPR" class="w-full text-xs border-gray-200 rounded-lg dark:bg-gray-800 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Manual / Tidak dari PR --</option>
                                    @foreach($approved_requests as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->request_number }} ({{ $pr->warehouse->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Supplier Tujuan</label>
                                <select wire:model="supplier_id" class="w-full text-xs border-gray-200 rounded-lg dark:bg-gray-800 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Kirim Ke Gudang</label>
                                <select wire:model="warehouse_id" class="w-full text-xs border-gray-200 rounded-lg dark:bg-gray-800 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Gudang --</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Date & Terms -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border-b pb-8 dark:border-gray-700">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tgl PO</label>
                                <input type="date" wire:model="po_date" class="w-full text-sm border-gray-200 rounded-lg dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Estimasi Kedatangan</label>
                                <input type="date" wire:model="expected_delivery_date" class="w-full text-sm border-gray-200 rounded-lg dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Termin Pembayaran (Hari)</label>
                                <input type="number" wire:model="payment_term" class="w-full text-sm border-gray-200 rounded-lg dark:bg-gray-700">
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-tighter">Daftar Detail Barang Pesanan</h4>
                                <button type="button" wire:click="addItem" class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1 rounded-md border border-indigo-100 hover:bg-indigo-100 font-bold transition-all">+ Item Manual</button>
                            </div>

                            <div class="border rounded-xl shadow-sm overflow-hidden dark:border-gray-700">
                                <table class="w-full text-[11px] text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-400 font-bold uppercase tracking-widest border-b dark:border-gray-700">
                                        <tr>
                                            <th class="px-3 py-3 w-1/3">Nama Barang</th>
                                            <th class="px-3 py-3 w-16 text-center">Qty</th>
                                            <th class="px-3 py-3 w-32 text-right">Harga Satuan</th>
                                            <th class="px-3 py-3 w-16 text-center">PPN%</th>
                                            <th class="px-3 py-3 w-32 text-right bg-indigo-50/20">Subtotal</th>
                                            <th class="px-3 py-3 w-8 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach($items as $index => $item)
                                            <tr class="group hover:bg-gray-50 transition-colors">
                                                <td class="px-3 py-3">
                                                    <div class="flex flex-col gap-1">
                                                        <select wire:model="items.{{ $index }}.item_id" class="w-full border-gray-200 rounded text-[11px] p-1.5 dark:bg-gray-800 focus:ring-1 focus:ring-indigo-500">
                                                            <option value="">-- Pilih Barang --</option>
                                                            @foreach($available_items as $ai)
                                                                <option value="{{ $ai->id }}">{{ $ai->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" wire:click="fetchPrice({{ $index }})" class="text-[9px] text-indigo-500 font-black uppercase tracking-tight hover:underline self-start">✨ Tarik Harga E-Catalog</button>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3">
                                                    <input type="number" wire:model.live="items.{{ $index }}.qty_ordered" wire:change="calculateTotals" class="w-full border-gray-200 rounded p-1.5 text-center font-bold dark:bg-gray-800 focus:ring-1 focus:ring-indigo-500">
                                                </td>
                                                <td class="px-3 py-3">
                                                    <div class="relative">
                                                        <span class="absolute left-1.5 top-1.5 text-gray-400">Rp</span>
                                                        <input type="number" wire:model.live="items.{{ $index }}.purchase_price" wire:change="calculateTotals" class="w-full border-gray-200 rounded p-1.5 text-right font-semibold dark:bg-gray-800 pl-6 focus:ring-1 focus:ring-indigo-500">
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <input type="number" wire:model.live="items.{{ $index }}.ppn_percentage" wire:change="calculateTotals" class="w-full border-gray-200 rounded p-1.5 text-center dark:bg-gray-800 focus:ring-1 focus:ring-indigo-500">
                                                </td>
                                                <td class="px-3 py-3 text-right font-black text-gray-700 bg-indigo-50/5 dark:text-gray-200">
                                                    <span class="text-[10px] text-gray-400 font-medium mr-1">Rp</span>
                                                    {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <button type="button" wire:click="removeItem({{ $index }})" class="text-red-300 hover:text-red-600 transition-colors opacity-0 group-hover:opacity-100">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Totals -->
                    <div class="px-8 py-6 bg-gray-900 text-white rounded-b-xl">
                        <div class="flex flex-col md:flex-row justify-between gap-8">
                            <div class="flex-1 space-y-3">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest">Catatan Penutup</label>
                                <textarea wire:model="notes" rows="3" class="w-full bg-gray-800 border-gray-700 rounded-lg text-sm text-gray-300 focus:ring-indigo-500" placeholder="Instruksi pengiriman, termin khusus, dll..."></textarea>
                            </div>
                            <div class="flex-none min-w-[300px] space-y-2">
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>SUBTOTAL ITEM</span>
                                    <span class="font-mono">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400 border-b border-gray-800 pb-2">
                                    <span>TOTAL PPN</span>
                                    <span class="font-mono">Rp {{ number_format($ppn_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-xs font-black text-indigo-400 uppercase">GRAND TOTAL</span>
                                    <span class="text-2xl font-black font-mono text-white">Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
                                </div>
                                <div class="pt-6 flex gap-3">
                                    <button type="button" wire:click="closeModal" class="flex-1 px-4 py-2 text-[10px] font-black text-gray-400 border border-gray-700 rounded-lg hover:bg-gray-800 transition-all uppercase tracking-widest shadow-sm">Batal</button>
                                    <button type="submit" class="flex-1 px-4 py-2 text-[10px] font-black text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-all uppercase tracking-widest shadow-xl shadow-indigo-500/20 active:scale-95">Finalisasi & Kirim PO</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
