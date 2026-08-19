<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight dark:text-white">Manajemen Retur</h2>
            <p class="text-slate-500 text-sm dark:text-slate-400">Kelola pengembalian barang ke Supplier dan Retur Internal</p>
        </div>
        @can('returns.create')
            <a href="{{ route('inventory.returns.create') }}" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-slate-100 font-bold text-sm">
                <i class="ph-bold ph-plus"></i> Buat Retur Baru
            </a>
        @endcan
    </div>

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center dark:bg-amber-500/15 dark:text-amber-400">
                <i class="ph ph-note-pencil text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Draft</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['draft'] }} <span class="text-sm font-normal text-slate-400 dark:text-slate-500">Dok</span></h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center dark:bg-indigo-500/15 dark:text-indigo-400">
                <i class="ph ph-clock text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Submitted</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['submitted'] }} <span class="text-sm font-normal text-slate-400 dark:text-slate-500">Dok</span></h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center dark:bg-rose-500/15 dark:text-rose-400">
                <i class="ph ph-truck text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Ke Supplier</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['supplier_return'] }} <span class="text-sm font-normal text-slate-400 dark:text-slate-500">Dok</span></h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 dark:bg-white/[0.03] dark:border-gray-800">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center dark:bg-emerald-500/15 dark:text-emerald-400">
                <i class="ph ph-arrows-left-right text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider dark:text-slate-500">Internal</p>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $stats['internal_return'] }} <span class="text-sm font-normal text-slate-400 dark:text-slate-500">Dok</span></h3>
            </div>
        </div>
    </div>

    {{-- Filters & Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row gap-4 justify-between items-center text-left dark:border-gray-800">
            <div class="relative w-full md:w-96 text-left">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input type="text" wire:model.live="search" placeholder="Cari nomor retur..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-slate-200 transition-all text-left dark:bg-white/[0.03] dark:text-white">
            </div>
            <div class="flex gap-3 w-full md:w-auto text-left">
                <select wire:model.live="type_filter" class="bg-slate-50 border-none rounded-xl text-sm py-2.5 focus:ring-2 focus:ring-slate-200 transition-all dark:bg-white/[0.03] dark:text-white">
                    <option value="">Semua Tipe</option>
                    <option value="supplier">Ke Supplier</option>
                    <option value="internal">Internal</option>
                </select>
                <select wire:model.live="status_filter" class="bg-slate-50 border-none rounded-xl text-sm py-2.5 focus:ring-2 focus:ring-slate-200 transition-all dark:bg-white/[0.03] dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="supplier_notified">Supplier Notified</option>
                    <option value="approved">Approved</option>
                    <option value="picked_up">Picked Up</option>
                    <option value="completed">Completed</option>
                </select>
                <select wire:model.live="warehouse_filter" class="bg-slate-50 border-none rounded-xl text-sm py-2.5 focus:ring-2 focus:ring-slate-200 transition-all dark:bg-white/[0.03] dark:text-white">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto text-left">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-white/[0.02]">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">No. Retur & Tgl</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Gudang</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Tujuan / Supplier</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right dark:text-slate-400">Nilai</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center dark:text-slate-400">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right dark:text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-gray-800">
                    @forelse($returns as $r)
                        <tr class="group hover:bg-slate-50/50 transition-all cursor-default text-left dark:hover:bg-white/[0.03]">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-700 dark:text-white">{{ $r->return_number }}</span>
                                <p class="text-[10px] text-slate-400 uppercase font-black tracking-tight dark:text-slate-500">{{ $r->return_date->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $r->type === 'supplier' ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400' }}">
                                        {{ $r->type }}
                                    </span>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-white">{{ $r->fromWarehouse->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($r->type === 'supplier')
                                    <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $r->supplier->name }}</p>
                                    <p class="text-[10px] text-slate-400 italic dark:text-slate-500">PO: {{ $r->po_number ?: '-' }}</p>
                                @else
                                    <p class="text-sm font-semibold text-slate-700 dark:text-white">Ke: {{ $r->toWarehouse->name }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-slate-800 tracking-tight dark:text-white">Rp {{ number_format($r->total_value) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border
                                    @if($r->status === 'draft') bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20
                                    @elseif($r->status === 'submitted') bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20
                                    @elseif($r->status === 'approved') bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20
                                    @elseif($r->status === 'completed') bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-500/15 dark:text-blue-400 dark:border-blue-500/20
                                    @else bg-slate-50 text-slate-600 border-slate-100 dark:bg-white/[0.03] dark:text-slate-300 dark:border-gray-800 @endif">
                                    {{ str_replace('_', ' ', $r->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @php
                                        $canEdit = ($r->status === 'draft');
                                        $canReview = ($r->status === 'submitted' && auth()->user()->can('returns.approve'));
                                    @endphp
                                    
                                    @if($canEdit || $canReview)
                                        <a href="{{ route('inventory.returns.edit', $r->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all dark:hover:bg-indigo-500/15" title="{{ $canReview ? 'Review & Approve' : 'Edit Dokumen' }}">
                                            <i class="ph {{ $canReview ? 'ph-check-square' : 'ph-pencil-line' }} text-lg"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('inventory.returns.edit', $r->id) }}?view=1" class="p-2 text-slate-400 hover:bg-slate-100 rounded-lg transition-all dark:text-slate-500 dark:hover:bg-white/[0.03]" title="Lihat Detail">
                                        <i class="ph ph-eye text-lg"></i>
                                    </a>

                                    @if(!in_array($r->status, ['approved', 'completed', 'picked_up']) && auth()->user()->can('returns.delete'))
                                        <button @click="$dispatch('confirm-delete', {
                                            id: {{ $r->id }},
                                            action: 'do-delete-return',
                                            message: 'Apakah Anda yakin ingin menghapus dokumen ini?'
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="ph ph-tray text-4xl text-slate-200 dark:text-slate-700"></i>
                                    <p class="text-slate-400 text-sm italic dark:text-slate-500">Belum ada data dokumen.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-6 border-t border-slate-50 dark:border-gray-800">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
