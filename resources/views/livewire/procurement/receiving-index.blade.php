<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-1 items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Terima / Faktur / Supplier..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
            </div>
            
            <select wire:model.live="status" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="posted">Posted (Selesai)</option>
            </select>
        </div>

        <a href="{{ route('procurement.receivings.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-500 text-white text-sm font-semibold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Penerimaan
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">No. Penerimaan</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">No. Faktur / Tanggal</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Supplier / PO</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Gudang</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Total Tagihan</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($receivings as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900 block">{{ $item->receiving_number }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ \Carbon\Carbon::parse($item->receiving_date)->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="text-gray-900 block font-medium">{{ $item->invoice_number }}</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->invoice_date)->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm border-b border-brand-100 text-brand-700 font-bold block">{{ $item->supplier->name }}</span>
                            @if($item->purchaseOrder)
                                <span class="text-[10px] text-gray-400 font-medium">RE: {{ $item->purchaseOrder->po_number }}</span>
                            @else
                                <span class="text-[10px] text-red-400 font-black uppercase tracking-tighter italic">Emergency / Tanpa PO</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->warehouse->name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-black text-gray-900">Rp{{ number_format($item->grand_total) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status === 'posted')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Posted
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($item->status === 'draft')
                                    <a href="{{ route('procurement.receivings.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>
                                @endif
                                <button class="p-2 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded-xl transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <p class="text-gray-400 italic text-sm">Belum ada data penerimaan barang.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($receivings instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $receivings->links() }}
        </div>
    @endif
</div>
