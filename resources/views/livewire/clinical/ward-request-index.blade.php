<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm shadow-indigo-50 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20 dark:shadow-none">
                <i class="ph ph-buildings text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Permintaan Unit / Ruangan</h2>
                <p class="text-sm text-slate-500 dark:text-gray-400">Manajemen amprahan stok dari Ruang Rawat ke Depo Farmasi.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('clinical.ward-requests.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-200 transition-all flex items-center gap-2 dark:shadow-none">
                <i class="ph ph-plus-circle"></i>
                Buat Permintaan Baru
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-white/[0.03] dark:border-gray-800 dark:text-white" placeholder="Cari No. Permintaan...">
                <div class="absolute left-3 top-2.5 text-slate-400 dark:text-gray-500">
                    <i class="ph ph-magnifying-glass"></i>
                </div>
            </div>
        </div>
        <div class="w-48">
            <select wire:model.live="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                <option value="">Semua Status</option>
                <option value="requested">Requested (Baru)</option>
                <option value="approved">Approved</option>
                <option value="partially_fulfilled">Diproses Sebagian</option>
                <option value="fulfilled">Selesai (Dikirim)</option>
            </select>
        </div>
        <div class="w-48">
            <select wire:model.live="warehouse_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                <option value="">Semua Sumber Depo</option>
                @foreach($pharmacies as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 border-b border-slate-100 uppercase tracking-wider dark:bg-white/[0.02] dark:text-gray-400 dark:border-gray-800">
                        <th class="px-6 py-4">No. Permintaan</th>
                        <th class="px-6 py-4">Unit Peminta</th>
                        <th class="px-6 py-4">Ditujukan Ke</th>
                        <th class="px-6 py-4">Waktu & Peminta</th>
                        <th class="px-6 py-4 text-center">Realisasi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm dark:divide-gray-800">
                    @forelse($requests as $req)
                        <tr wire:key="req-{{ $req->id }}" class="hover:bg-slate-50/50 transition-colors dark:hover:bg-white/[0.03]">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $req->request_number }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700 dark:text-gray-300">
                                {{ $req->serviceUnit->name }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600 italic dark:text-gray-400">
                                {{ $req->warehouse->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium dark:text-gray-300">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase dark:text-gray-500">{{ $req->requestedBy->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $itemCount = $req->details->count();
                                    $fulfilledCount = $req->details->where('qty_fulfilled', '>', 0)->count();
                                    $percent = $itemCount > 0 ? round(($fulfilledCount / $itemCount) * 100) : 0;
                                @endphp
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] font-bold text-slate-500 mb-1 dark:text-gray-400">{{ $fulfilledCount }}/{{ $itemCount }} Item</span>
                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200 dark:bg-gray-800 dark:border-gray-700">
                                        <div class="h-full bg-indigo-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($req->status) {
                                        'requested' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20',
                                        'approved' => 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20',
                                        'fulfilled' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20',
                                        'rejected' => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20',
                                        default => 'bg-slate-50 text-slate-400 border-slate-100 dark:bg-white/[0.03] dark:text-gray-500 dark:border-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[10px] font-bold border rounded {{ $statusColor }} uppercase">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                   <button wire:click="viewDetails({{ $req->id }})" class="p-2 text-slate-400 hover:bg-slate-100 rounded-lg transition-all group dark:text-gray-500 dark:hover:bg-white/[0.05]" title="Lihat Detail Permintaan">
                                       <i class="ph ph-eye text-lg group-hover:scale-110 transition-transform"></i>
                                   </button>
                                   @if($req->status === 'requested')
                                       <button wire:click="processRequest({{ $req->id }})" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-all group dark:text-amber-400 dark:hover:bg-amber-500/10" title="Verifikasi & Proses Stok">
                                           <i class="ph ph-check-square-offset text-lg group-hover:scale-110 transition-transform text-amber-700 dark:text-amber-400"></i>
                                       </button>
                                       <button wire:click="editRequest({{ $req->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all group dark:text-indigo-400 dark:hover:bg-indigo-500/10" title="Edit Permintaan">
                                           <i class="ph ph-note-pencil text-lg group-hover:scale-110 transition-transform"></i>
                                       </button>
                                   @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic dark:text-gray-500">
                                Belum ada permintaan unit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 dark:bg-white/[0.02] dark:border-gray-800">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detail Permintaan -->
    @if($showDetailModal && $selectedRequest)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/35 backdrop-blur-[2px]" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }" wire:click.self="closeModals">
            <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 dark:bg-gray-900">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50 dark:border-gray-800 dark:bg-white/[0.02]">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Detail Permintaan: {{ $selectedRequest->request_number }}</h3>
                        <p class="text-xs text-slate-500 dark:text-gray-400">Dari: {{ $selectedRequest->serviceUnit->name }} ke {{ $selectedRequest->warehouse->name }}</p>
                    </div>
                    <button wire:click="closeModals" class="p-2 hover:bg-slate-200 rounded-full transition-colors dark:hover:bg-gray-800">
                        <i class="ph ph-x font-bold"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 dark:text-gray-500">Diminta Oleh</p>
                            <p class="font-bold text-slate-700 dark:text-gray-300">{{ $selectedRequest->requestedBy->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 dark:text-gray-500">Tanggal Permintaan</p>
                            <p class="font-bold text-slate-700 dark:text-gray-300">{{ $selectedRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div>
                        <table class="w-full text-left border-collapse rounded-xl overflow-hidden border border-slate-100 dark:border-gray-800">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider dark:bg-white/[0.02] dark:text-gray-400">
                                    <th class="px-4 py-3">Nama Barang</th>
                                    <th class="px-4 py-3 text-center">Jumlah Diminta</th>
                                    <th class="px-4 py-3 text-center">Realisasi</th>
                                    <th class="px-4 py-3">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs dark:divide-gray-800">
                                @foreach($selectedRequest->details as $detail)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.03]">
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-slate-800 dark:text-white">{{ $detail->item->name }}</div>
                                            <div class="text-[10px] text-slate-400 dark:text-gray-500">{{ $detail->item->code }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center font-black text-slate-700 dark:text-gray-300">{{ $detail->qty_requested }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full font-bold {{ $detail->qty_fulfilled >= $detail->qty_requested ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400' }}">
                                                {{ $detail->qty_fulfilled ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 italic text-slate-400 italic text-[10px] dark:text-gray-500">{{ $detail->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 dark:bg-white/[0.02] dark:border-gray-800">
                    <button wire:click="closeModals" class="px-6 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-gray-800">Tutup</button>
                    @if($selectedRequest->status === 'requested')
                        <button wire:click="processRequest({{ $selectedRequest->id }})" class="px-6 py-2 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-md hover:shadow-amber-100 transition-all flex items-center gap-2 dark:shadow-none">
                            <i class="ph-fill ph-check-square"></i> Proses Sekarang
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Proses -->
    @if($showConfirmModal && $selectedRequest)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/35 backdrop-blur-[2px]" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }" wire:click.self="closeModals">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-200 dark:bg-gray-900">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-amber-50 text-amber-700 dark:border-gray-800 dark:bg-amber-500/10 dark:text-amber-400">
                    <h3 class="font-bold flex items-center gap-2">
                        <i class="ph ph-check-square"></i> Verifikasi & Proses Stok
                    </h3>
                    <button wire:click="closeModals" class="p-1 hover:bg-amber-100 rounded-full transition-colors dark:hover:bg-amber-500/20">
                        <i class="ph ph-x font-bold"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl dark:bg-amber-500/10 dark:border-amber-500/20">
                        <p class="text-sm text-amber-800 leading-relaxed dark:text-amber-300">
                            Anda akan memproses permintaan item dari <strong>{{ $selectedRequest->serviceUnit->name }}</strong>.
                            Sistem akan melakukan pemotongan stok otomatis sesuai dengan ketersediaan di <strong>{{ $selectedRequest->warehouse->name }}</strong>.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider italic dark:text-gray-500">Rangkuman Item</p>
                        @foreach($selectedRequest->details as $detail)
                            <div class="flex justify-between items-center text-sm p-3 bg-slate-50 rounded-lg border border-slate-100 dark:bg-white/[0.02] dark:border-gray-800">
                                <div class="font-bold text-slate-700 dark:text-gray-300">{{ $detail->item->name }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-400 dark:text-gray-500">Qty:</span>
                                    <span class="font-black text-slate-800 dark:text-white">{{ $detail->qty_requested }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 flex gap-3 bg-slate-50 dark:border-gray-800 dark:bg-white/[0.02]">
                    <button wire:click="closeModals" class="flex-1 py-3 text-sm font-bold text-slate-600 hover:bg-slate-200 rounded-xl transition-colors dark:text-gray-400 dark:hover:bg-gray-800">Batal</button>
                    <button wire:click="confirmFulfillment" class="flex-[2] py-3 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow-lg shadow-amber-100 transition-all dark:shadow-none">
                        Ya, Verifikasi & Potong Stok
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
