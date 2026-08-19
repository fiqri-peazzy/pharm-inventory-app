<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex flex-1 flex-wrap items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-gray-500">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Jurnal / Referensi..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
            </div>
            
            <select wire:model.live="type" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
                <option value="">Semua Tipe</option>
                <option value="standard">Standard</option>
                <option value="adjusting">Adjusting</option>
                <option value="closing">Closing</option>
            </select>

            <select wire:model.live="status" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="posted">Posted</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="dateFrom" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
                <span class="text-gray-400 dark:text-gray-500">-</span>
                <input type="date" wire:model.live="dateTo" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
            </div>
        </div>

        <a href="{{ route('accounting.journals.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-500 text-white text-sm font-semibold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Jurnal Manual
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal / No. Jurnal</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipe / Referensi</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Keterangan</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-right dark:text-gray-400">Debit</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-right dark:text-gray-400">Kredit</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-center dark:text-gray-400">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($journals as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors dark:hover:bg-white/[0.03]">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900 block dark:text-white">{{ $item->journal_number }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-black tracking-widest dark:text-gray-500">{{ \Carbon\Carbon::parse($item->journal_date)->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 uppercase tracking-tighter dark:bg-gray-800 dark:text-gray-300">{{ $item->type }}</span>
                            @if($item->reference)
                                <span class="text-[11px] text-gray-500 block mt-1 font-medium dark:text-gray-400">Ref: {{ $item->reference }}</span>
                            @endif
                            @if($item->transaction_type)
                                <span class="text-[9px] text-brand-500 font-bold uppercase tracking-widest mt-0.5 block italic border-l-2 border-brand-200 pl-1">{{ $item->transaction_type }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 line-clamp-2 max-w-xs dark:text-gray-300">{{ $item->description }}</p>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                            {{ number_format($item->total_debit) }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                            {{ number_format($item->total_credit) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->status === 'posted')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Posted
                                </span>
                            @elseif($item->status === 'draft')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Draft
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-50 text-red-700 border border-red-100 italic">
                                    {{ $item->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('accounting.journals.show', $item->id) }}" class="p-2 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded-xl transition-all dark:text-gray-500" title="Lihat Detail">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </a>
                                @if($item->status === 'draft')
                                    <a href="{{ route('accounting.journals.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all dark:text-gray-500" title="Edit Jurnal">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic text-sm dark:text-gray-500">
                            Tidak ada data jurnal ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($journals instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $journals->links() }}
        </div>
    @endif
</div>
