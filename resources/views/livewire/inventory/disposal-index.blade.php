<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight">Pemusnahan & Retur</h2>
            <p class="text-slate-500 text-sm">Kelola penghapusan stok (Disposal) dan Pengembalian ke Supplier</p>
        </div>
        @can('disposals.create')
            <a href="{{ route('inventory.disposals.create') }}" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-slate-100 font-bold text-sm">
                <i class="ph-bold ph-plus"></i> Buat Dokumen Baru
            </a>
        @endcan
    </div>

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="ph ph-file-text text-xl"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Draft</p>
                <h3 class="text-lg font-black text-slate-800">{{ $stats['draft'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="ph ph-paper-plane-tilt text-xl"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Review</p>
                <h3 class="text-lg font-black text-slate-800">{{ $stats['submitted'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="ph ph-check-circle text-xl"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Approved</p>
                <h3 class="text-lg font-black text-slate-800">{{ $stats['approved'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="ph ph-lightning text-xl"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Executed</p>
                <h3 class="text-lg font-black text-slate-800">{{ $stats['executed'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="ph ph-check-square text-xl"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Posted</p>
                <h3 class="text-lg font-black text-slate-800">{{ $stats['posted'] }}</h3>
            </div>
        </div>
    </div>

    {{-- Filters & Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row gap-4 justify-between items-center text-left">
            <div class="relative w-full md:w-96">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" wire:model.live="search" placeholder="Cari nomor dokumen..." 
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-slate-200 transition-all">
            </div>
            <div class="flex gap-3 w-full md:w-auto">
                <select wire:model.live="type_filter" class="bg-slate-50 border-none rounded-xl text-sm py-2.5 focus:ring-2 focus:ring-slate-200 transition-all">
                    <option value="">Semua Tipe</option>
                    <option value="disposal">Pemusnahan</option>
                    <option value="return_to_supplier">Retur Supplier</option>
                </select>
                <select wire:model.live="status_filter" class="bg-slate-50 border-none rounded-xl text-sm py-2.5 focus:ring-2 focus:ring-slate-200 transition-all">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="posted">Posted</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto text-left">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Dokumen</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Gudang</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">BA Resmi</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($disposals as $d)
                        <tr class="group hover:bg-slate-50/50 transition-all cursor-default text-left">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-700">{{ $d->disposal_number }}</span>
                                <p class="text-[10px] text-slate-400 uppercase font-black tracking-tight">{{ $d->disposal_date->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $d->type === 'disposal' ? 'bg-rose-50 text-rose-600' : 'bg-orange-50 text-orange-600' }}">
                                    {{ str_replace('_', ' ', $d->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-700">{{ $d->warehouse->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-bold text-slate-500 italic">{{ $d->ba_number ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border 
                                    @if($d->status === 'draft') bg-amber-50 text-amber-600 border-amber-100 
                                    @elseif($d->status === 'submitted') bg-indigo-50 text-indigo-600 border-indigo-100 
                                    @elseif($d->status === 'approved') bg-blue-50 text-blue-600 border-blue-100
                                    @elseif($d->status === 'executed') bg-purple-50 text-purple-600 border-purple-100
                                    @else bg-emerald-50 text-emerald-600 border-emerald-100 @endif">
                                    {{ $d->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @php
                                        $canEdit = ($d->status === 'draft');
                                        $canReview = ($d->status === 'submitted' && auth()->user()->hasRole(['super-admin', 'kepala-farmasi', 'direktur']));
                                        $canExecute = ($d->status === 'approved');
                                        $canPost = ($d->status === 'executed');
                                    @endphp
                                    
                                    @if($canEdit || $canReview || $canExecute || $canPost)
                                        <a href="{{ route('inventory.disposals.edit', $d->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" 
                                            title="{{ $canReview ? 'Review & Approve' : ($canExecute ? 'Eksekusi Fisik' : ($canPost ? 'Final Posting' : 'Edit Dokumen')) }}">
                                            <i class="ph ph-pencil-line text-lg"></i>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('inventory.disposals.edit', $d->id) }}?view=1" class="p-2 text-slate-400 hover:bg-slate-100 rounded-lg transition-all" title="Lihat Detail">
                                        <i class="ph ph-eye text-lg"></i>
                                    </a>

                                    @if($d->status !== 'posted' && auth()->user()->can('disposals.delete'))
                                        <button @click="$dispatch('confirm-delete', { 
                                            id: {{ $d->id }}, 
                                            action: 'do-delete-disposal',
                                            message: 'Apakah Anda yakin ingin menghapus dokumen ini?' 
                                        })" 
                                            class="p-2 text-rose-400 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Dokumen">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-left">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="ph ph-tray text-4xl text-slate-200"></i>
                                    <p class="text-slate-400 text-sm italic">Belum ada data dokumen.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($disposals->hasPages())
            <div class="p-6 border-t border-slate-50">
                {{ $disposals->links() }}
            </div>
        @endif
    </div>
</div>
