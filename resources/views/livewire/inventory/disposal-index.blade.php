<div>
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex flex-1 flex-wrap items-center gap-3">
            <div class="relative w-full md:w-64">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Berita Acara..." class="w-full rounded-xl border border-gray-100 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 transition-all shadow-sm">
            </div>

            <select wire:model.live="type" class="rounded-xl border border-gray-100 bg-white py-2.5 px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <option value="">Semua Tipe</option>
                <option value="disposal">Pemusnahan (Disposal)</option>
                <option value="return_to_supplier">Retur ke Supplier</option>
            </select>

            <select wire:model.live="status" class="rounded-xl border border-gray-100 bg-white py-2.5 px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="posted">Posted ✓</option>
            </select>
        </div>
        
        <a href="{{ route('inventory.disposals.create') }}" class="flex items-center gap-2 rounded-xl bg-red-600 px-6 py-2.5 text-xs font-black text-white hover:bg-red-700 shadow-lg shadow-red-600/20 transition-all uppercase tracking-widest">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"></path></svg>
            Buat Berita Acara
        </a>
    </div>

    <div class="overflow-hidden bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                <tr>
                    <th class="px-6 py-4">BA Disposal / Retur</th>
                    <th class="px-6 py-4">Gudang</th>
                    <th class="px-6 py-4 text-center">Tgl Proses</th>
                    <th class="px-6 py-4 text-center">Tipe</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($disposals as $d)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white group-hover:text-red-500 uppercase tracking-tight">{{ $d->disposal_number }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $d->creator->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">{{ $d->warehouse->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">{{ $d->disposal_date->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest {{ $d->type === 'disposal' ? 'text-red-500' : 'text-orange-500' }}">
                                {{ str_replace('_', ' ', $d->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($d->status === 'draft')
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-[9px] font-black text-gray-600 uppercase tracking-widest ring-1 ring-inset ring-gray-500/10 shadow-sm">Draft</span>
                            @else
                                <span class="rounded-full bg-green-100 px-2 py-1 text-[9px] font-black text-green-700 uppercase tracking-widest ring-1 ring-inset ring-green-600/20 shadow-sm">Posted ✓</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($d->status === 'draft')
                                    <a href="{{ route('inventory.disposals.edit', $d->id) }}" class="p-2 text-gray-300 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>
                                    <button wire:click="delete({{ $d->id }})" wire:confirm="Hapus Berita Acara ini?" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center opacity-40 italic text-gray-400 text-sm">
                             <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span class="text-xs uppercase font-black tracking-widest">Belum ada Berita Acara Disposal</span>
                             </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($disposals instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $disposals->links() }}
        </div>
    @endif
</div>
