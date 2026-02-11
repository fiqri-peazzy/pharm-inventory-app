<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm shadow-indigo-50">
                <i class="ph ph-buildings text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Permintaan Unit / Ruangan</h2>
                <p class="text-sm text-slate-500">Manajemen amprahan stok dari Ruang Rawat ke Depo Farmasi.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('clinical.ward-requests.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-200 transition-all flex items-center gap-2">
                <i class="ph ph-plus-circle"></i>
                Buat Permintaan Baru
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Cari No. Permintaan...">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <i class="ph ph-magnifying-glass"></i>
                </div>
            </div>
        </div>
        <div class="w-48">
            <select wire:model.live="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Status</option>
                <option value="requested">Requested (Baru)</option>
                <option value="approved">Approved</option>
                <option value="partially_fulfilled">Diproses Sebagian</option>
                <option value="fulfilled">Selesai (Dikirim)</option>
            </select>
        </div>
        <div class="w-48">
            <select wire:model.live="warehouse_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Sumber Depo</option>
                @foreach($pharmacies as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 border-b border-slate-100 uppercase tracking-wider">
                        <th class="px-6 py-4">No. Permintaan</th>
                        <th class="px-6 py-4">Unit Peminta</th>
                        <th class="px-6 py-4">Ditujukan Ke</th>
                        <th class="px-6 py-4">Waktu & Peminta</th>
                        <th class="px-6 py-4 text-center">Realisasi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $req->request_number }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ $req->serviceUnit->name }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600 italic">
                                {{ $req->warehouse->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $req->requestedBy->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $itemCount = $req->details->count();
                                    $fulfilledCount = $req->details->where('qty_fulfilled', '>', 0)->count();
                                    $percent = $itemCount > 0 ? round(($fulfilledCount / $itemCount) * 100) : 0;
                                @endphp
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] font-bold text-slate-500 mb-1">{{ $fulfilledCount }}/{{ $itemCount }} Item</span>
                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                                        <div class="h-full bg-indigo-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($req->status) {
                                        'requested' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'approved' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                        'fulfilled' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'rejected' => 'bg-red-50 text-red-600 border-red-100',
                                        default => 'bg-slate-50 text-slate-400 border-slate-100'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[10px] font-bold border rounded {{ $statusColor }} uppercase">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Detail">
                                        <i class="ph ph-eye text-lg"></i>
                                    </button>
                                    @if($req->status === 'requested')
                                        <button class="p-2 text-slate-400 hover:bg-slate-50 rounded-lg transition-all">
                                            <i class="ph ph-note-pencil text-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                                Belum ada permintaan unit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($requests->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
