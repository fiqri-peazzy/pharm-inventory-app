<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Stock Opname</h2>
            <p class="text-slate-500 text-sm dark:text-slate-400">Validasi stok fisik barang di gudang & depo</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('inventory.stock-opnames.create') }}" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm shadow-indigo-100">
                <i class="ph ph-plus-circle text-lg"></i>
                <span class="font-bold text-sm">Target Opname Baru</span>
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center dark:bg-amber-500/15 dark:text-amber-400">
                <i class="ph ph-clock-countdown text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Menunggu Hitung</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['draft'] }} Document</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center dark:bg-indigo-500/15 dark:text-indigo-400">
                <i class="ph ph-magnifying-glass text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Sedang Review</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['submitted'] }} Document</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center dark:bg-emerald-500/15 dark:text-emerald-400">
                <i class="ph ph-check-circle text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Selesai (Posted)</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['posted'] }} Document</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 {{ $stats['significant'] > 0 ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400' : 'bg-slate-50 text-slate-400 dark:bg-white/[0.03] dark:text-slate-500' }} rounded-xl flex items-center justify-center">
                <i class="ph ph-warning-diamond text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Selisih Signifikan</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['significant'] }} Item</h3>
            </div>
        </div>
    </div>

    <!-- Filters & List -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row gap-4 dark:border-gray-800">
            <div class="relative flex-1">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input type="text" wire:model.live="search" placeholder="Cari nomor opname..." class="w-full pl-12 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:bg-white/[0.03] dark:text-white">
            </div>
            <select wire:model.live="warehouse_filter" class="px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:bg-white/[0.03] dark:text-white">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="status_filter" class="px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all dark:bg-white/[0.03] dark:text-white">
                <option value="">Semua Status</option>
                <option value="draft">Draft (Menunggu Hitung)</option>
                <option value="submitted">Submitted (Reviewing)</option>
                <option value="posted">Posted (Selesai)</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-white/[0.02]">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">No. Opname</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Gudang</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">PIC</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center dark:text-slate-400">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right dark:text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-gray-800">
                    @forelse($opnames as $op)
                        <tr class="hover:bg-slate-50/50 transition-colors dark:hover:bg-white/[0.03]">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-700 dark:text-white">{{ $op->opname_number }}</span>
                                <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter dark:text-slate-500">{{ $op->type }} Opname</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $op->opname_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $op->warehouse->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $op->pic->name }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColor = match($op->status) {
                                        'draft' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20',
                                        'submitted' => 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20',
                                        'posted' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20',
                                        default => 'bg-slate-50 text-slate-600 border-slate-100 dark:bg-white/[0.03] dark:text-slate-300 dark:border-gray-800'
                                    };
                                @endphp
                                <span class="px-3 py-1 text-[10px] font-bold border rounded-full {{ $statusColor }}">
                                    {{ strtoupper($op->status) }}
                                </span>
                            </td>
                                    <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @php
                                        $canEdit = ($op->status === 'draft');
                                        $canReview = ($op->status === 'submitted' && auth()->user()->can('stock-opnames.approve'));
                                    @endphp
                                    
                                    @if($canEdit || $canReview)
                                        <a href="{{ route('inventory.stock-opnames.edit', $op->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all dark:hover:bg-indigo-500/15" title="{{ $canReview ? 'Review & Setujui' : 'Mulai Hitung' }}">
                                            <i class="ph {{ $canReview ? 'ph-check-square' : 'ph-pencil-line' }} text-lg"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('inventory.stock-opnames.edit', $op->id) }}?view=1" class="p-2 text-slate-400 hover:bg-slate-100 rounded-lg transition-all dark:text-slate-500 dark:hover:bg-white/[0.03]" title="Lihat Detail">
                                        <i class="ph ph-eye text-lg"></i>
                                    </a>
                                    @if($op->status !== 'posted' && auth()->user()->can('stock-opnames.delete'))
                                        <button @click="$dispatch('confirm-delete', {
                                            id: {{ $op->id }},
                                            action: 'do-delete-opname',
                                            message: 'Apakah Anda yakin ingin menghapus dokumen opname ini? Data yang dihapus tidak dapat dikembalikan.'
                                        })"
                                            class="p-2 text-rose-400 hover:bg-rose-50 rounded-lg transition-all dark:hover:bg-rose-500/15" title="Hapus Dokumen">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic dark:text-slate-500">
                                Belum ada riwayat stock opname.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $opnames->links() }}
        </div>
    </div>
</div>
