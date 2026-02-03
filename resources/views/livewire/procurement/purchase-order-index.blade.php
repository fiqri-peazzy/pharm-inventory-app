<div>
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex flex-1 flex-wrap items-center gap-3">
            <div class="relative w-full md:w-64">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. PO..." class="w-full rounded-xl border border-gray-100 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 transition-all shadow-sm">
            </div>

            <select wire:model.live="status" class="rounded-xl border border-gray-100 bg-white py-2.5 px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="sent">Sent 🚀</option>
                <option value="partial_received">Partial Received</option>
                <option value="completed">Completed ✓</option>
                <option value="cancelled">Cancelled ✕</option>
            </select>
        </div>
        
        <a href="{{ route('procurement.orders.create') }}" class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-2.5 text-xs font-black text-white hover:bg-brand-600 shadow-lg shadow-brand-500/20 transition-all uppercase tracking-widest">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"></path></svg>
            Pesanan Baru (PO)
        </a>
    </div>

    <div class="overflow-hidden bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                <tr>
                    <th class="px-6 py-4">Informasi Dokumen</th>
                    <th class="px-6 py-4">Supplier & Tujuan</th>
                    <th class="px-6 py-4 text-center">Tgl Pesan</th>
                    <th class="px-6 py-4 text-right">Grand Total</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white group-hover:text-brand-500 uppercase tracking-tight">{{ $order->po_number }}</span>
                                <span class="text-[10px] text-gray-400">Oleh: {{ $order->creator->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $order->supplier->name ?? 'Internal Store' }}</span>
                                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">{{ $order->warehouse->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($order->po_date)->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <span class="text-[13px] font-black text-gray-900 dark:text-white leading-none">Rp{{ number_format($order->grand_total) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-700 ring-gray-600/10',
                                    'sent' => 'bg-blue-100 text-blue-700 ring-blue-600/10',
                                    'partial_received' => 'bg-orange-100 text-orange-700 ring-orange-600/10',
                                    'completed' => 'bg-green-100 text-green-700 ring-green-600/10',
                                    'cancelled' => 'bg-red-100 text-red-700 ring-red-600/10',
                                ];
                                $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $color }} ring-1 ring-inset shadow-sm">
                                {{ str_replace('_', ' ', $order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2" wire:key="po-actions-{{ $order->id }}">
                                @if(in_array($order->status, ['draft', 'sent']))
                                    <a href="{{ route('procurement.orders.edit', $order->id) }}" class="p-2 text-gray-300 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>
                                @endif
                                <a href="{{ route('procurement.orders.print', $order->id) }}" target="_blank" class="p-2 text-gray-300 hover:text-blue-500 hover:bg-blue-50 rounded-xl transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                </a>
                                @if(!in_array($order->status, ['completed', 'cancelled']))
                                    <button wire:click="cancelOrder({{ $order->id }})" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center opacity-40 italic text-gray-400 text-sm">
                             <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-xs uppercase font-black tracking-widest">Belum ada data pesanan (PO)</span>
                             </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
