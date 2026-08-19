<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight dark:text-white">Adjustment Stok</h2>
            <p class="text-slate-500 text-sm dark:text-gray-400">Kelola penyesuaian stok manual (Plus/Minus)</p>
        </div>
        @can('stock-adjustments.create')
            <a href="{{ route('inventory.adjustments.create') }}" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-slate-100 font-bold text-sm dark:shadow-black/30">
                <i class="ph-bold ph-plus"></i> Buat Adjustment
            </a>
        @endcan
    </div>

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center dark:bg-amber-500/15 dark:text-amber-400">
                <i class="ph ph-note-pencil text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-gray-500">Draft</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['draft'] }} <span class="text-sm font-normal text-slate-400 dark:text-gray-500">Dokumen</span></h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center dark:bg-indigo-500/15 dark:text-indigo-400">
                <i class="ph ph-clock text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-gray-500">Menunggu Review</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['submitted'] }} <span class="text-sm font-normal text-slate-400 dark:text-gray-500">Dokumen</span></h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center dark:bg-emerald-500/15 dark:text-emerald-400">
                <i class="ph ph-check-circle text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-gray-500">Terbit (Posted)</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['posted'] }} <span class="text-sm font-normal text-slate-400 dark:text-gray-500">Dokumen</span></h3>
            </div>
        </div>
    </div>

    {{-- Filters & Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row gap-4 justify-between items-center dark:border-gray-800">
            <div class="relative w-full md:w-96">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-gray-500"></i>
                <input type="text" wire:model.live="search" placeholder="Cari nomor adjustment..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-slate-200 transition-all dark:bg-white/[0.03] dark:text-white dark:focus:ring-gray-700">
            </div>
            <div class="flex gap-3 w-full md:w-auto">
                <select wire:model.live="warehouse_filter" class="bg-slate-50 border-none rounded-xl text-sm py-2.5 focus:ring-2 focus:ring-slate-200 transition-all dark:bg-white/[0.03] dark:text-gray-300 dark:focus:ring-gray-700">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="status_filter" class="bg-slate-50 border-none rounded-xl text-sm py-2.5 focus:ring-2 focus:ring-slate-200 transition-all dark:bg-white/[0.03] dark:text-gray-300 dark:focus:ring-gray-700">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="posted">Posted</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-white/[0.02]">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-gray-400">Nomor Adjust</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-gray-400">Gudang</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-gray-400">Kategori / Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right dark:text-gray-400">Total Nilai</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center dark:text-gray-400">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-gray-800">
                    @forelse($adjustments as $adj)
                        <tr class="group hover:bg-slate-50/50 transition-all cursor-default dark:hover:bg-white/[0.03]">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-700 dark:text-gray-300">{{ $adj->adjustment_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-700 dark:text-gray-300">{{ $adj->warehouse->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-black text-indigo-400 leading-none mb-1 uppercase tracking-tighter dark:text-indigo-400">{{ $adj->reason_category }}</p>
                                <p class="text-[10px] text-slate-500 font-medium italic dark:text-gray-400">{{ $adj->adjustment_date ? $adj->adjustment_date->format('d M Y') : '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-black text-slate-700 tracking-tight italic dark:text-gray-300">Rp {{ number_format($adj->total_value) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border
                                    @if($adj->status === 'draft') bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20
                                    @elseif($adj->status === 'submitted') bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20
                                    @else bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20 @endif">
                                    {{ $adj->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @php
                                        $canEdit = ($adj->status === 'draft');
                                        $canReview = ($adj->status === 'submitted' && auth()->user()->can('stock-adjustments.approve'));
                                    @endphp

                                    @if($canEdit || $canReview)
                                        <a href="{{ route('inventory.adjustments.edit', $adj->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all dark:text-indigo-400 dark:hover:bg-indigo-500/15" title="{{ $canReview ? 'Review & Setujui' : 'Edit Adjustment' }}">
                                            <i class="ph {{ $canReview ? 'ph-check-square' : 'ph-pencil-line' }} text-lg"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('inventory.adjustments.edit', $adj->id) }}?view=1" class="p-2 text-slate-400 hover:bg-slate-100 rounded-lg transition-all dark:text-gray-500 dark:hover:bg-white/[0.03]" title="Lihat Detail">
                                        <i class="ph ph-eye text-lg"></i>
                                    </a>

                                    @if($adj->status !== 'posted' && auth()->user()->can('stock-adjustments.delete'))
                                        <button @click="$dispatch('confirm-delete', {
                                            id: {{ $adj->id }},
                                            action: 'do-delete-adjustment',
                                            message: 'Apakah Anda yakin ingin menghapus dokumen adjustment ini?'
                                        })"
                                            class="p-2 text-rose-400 hover:bg-rose-50 rounded-lg transition-all dark:text-rose-400 dark:hover:bg-rose-500/15" title="Hapus Dokumen">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="ph ph-tray text-4xl text-slate-200 dark:text-gray-800"></i>
                                    <p class="text-slate-400 text-sm italic dark:text-gray-500">Belum ada data adjustment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($adjustments->hasPages())
            <div class="p-6 border-t border-slate-50 dark:border-gray-800">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
</div>
