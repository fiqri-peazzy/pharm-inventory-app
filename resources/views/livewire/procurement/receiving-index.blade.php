<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex flex-1 items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-gray-500">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Terima / Faktur / Supplier..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:border-gray-800 dark:bg-white/[0.03] dark:text-white dark:focus:bg-white/[0.05]">
            </div>

            <select wire:model.live="status" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:border-gray-800 dark:bg-white/[0.03] dark:text-white dark:focus:bg-white/[0.05]">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted (Perlu Approve)</option>
                <option value="posted">Posted (Selesai)</option>
            </select>
        </div>

        <a href="{{ route('procurement.receivings.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-500 text-white text-sm font-semibold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all dark:shadow-none">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Penerimaan
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 dark:bg-white/[0.02] dark:border-gray-800">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">No. Penerimaan</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">No. Faktur / Tanggal</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Supplier / PO</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Gudang</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Tagihan</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($receivings as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors dark:hover:bg-white/[0.03]">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900 block dark:text-white">{{ $item->receiving_number }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-black tracking-widest dark:text-gray-500">{{ \Carbon\Carbon::parse($item->receiving_date)->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="text-gray-900 block font-medium dark:text-white">{{ $item->invoice_number }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($item->invoice_date)->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm border-b border-brand-100 text-brand-700 font-bold block dark:border-brand-500/30 dark:text-brand-400">{{ $item->supplier->name }}</span>
                            @if($item->purchaseOrder)
                                <span class="text-[10px] text-gray-400 font-medium dark:text-gray-500">RE: {{ $item->purchaseOrder->po_number }}</span>
                            @else
                                <span class="text-[10px] text-red-400 font-black uppercase tracking-tighter italic dark:text-red-400">Emergency / Tanpa PO</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $item->warehouse->name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-black text-gray-900 block dark:text-white">Rp{{ number_format($item->grand_total) }}</span>
                            @if($item->invoice_file)
                                <a href="{{ asset('storage/' . $item->invoice_file) }}" target="_blank" class="text-[9px] text-brand-600 font-bold hover:underline flex items-center gap-1 mt-1 uppercase">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                    Cek Faktur
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status === 'posted')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-50 text-green-700 border border-green-100 dark:bg-green-500/15 dark:text-green-400 dark:border-green-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Posted
                                </span>
                            @elseif($item->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100 italic dark:bg-blue-500/15 dark:text-blue-400 dark:border-blue-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Submitted
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($item->status === 'submitted')
                                    <button wire:click="post({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin melakukan posting stok untuk penerimaan ini? Tindakan ini tidak dapat dibatalkan." class="p-2 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all dark:text-blue-400 dark:hover:bg-blue-500/15" title="Posting ke Stok">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    </button>
                                @endif
                                @if($item->status === 'draft')
                                    <a href="{{ route('procurement.receivings.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all dark:text-gray-500 dark:hover:bg-brand-500/15" title="Edit Draft">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>
                                @endif
                                @if($item->status === 'posted')
                                    <a href="{{ route('procurement.receivings.print', $item->id) }}" target="_blank" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all dark:text-gray-500 dark:hover:bg-brand-500/15" title="Cetak Berita Acara (BA)">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    </a>
                                @endif
                                @if($item->status !== 'draft')
                                    <a href="{{ route('procurement.receivings.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded-xl transition-all dark:text-gray-500 dark:hover:bg-blue-500/15" title="Lihat Detail">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-200 mb-4 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <p class="text-gray-400 italic text-sm dark:text-gray-500">Belum ada data penerimaan barang.</p>
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
