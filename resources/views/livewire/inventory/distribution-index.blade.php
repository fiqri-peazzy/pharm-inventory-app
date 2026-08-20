<div class="space-y-4">
    <div class="flex items-center justify-between bg-white dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">Distribusi & Mutasi Stok</h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mt-1 block">Monitoring Pergerakan Barang Antar Instalasi</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.distributions.request') }}" class="px-5 py-2.5 bg-brand-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-brand-600 transition-all shadow-md flex items-center gap-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Buat Permintaan
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <input type="text" wire:model.live="search" placeholder="Cari No. Distribusi / Gudang..." class="w-full bg-white dark:bg-white/[0.03] border-gray-200 dark:border-gray-800 rounded-xl text-sm px-4 py-3 focus:ring-brand-500 focus:border-brand-500 transition-all font-medium">
        </div>
        <div>
            <select wire:model.live="status" class="w-full bg-white dark:bg-white/[0.03] border-gray-200 dark:border-gray-800 rounded-xl text-sm px-4 py-3 focus:ring-brand-500 focus:border-brand-500 transition-all font-bold text-gray-600 dark:text-gray-300">
                <option value="">Semua Status</option>
                <option value="requested">Requested</option>
                <option value="approved">Approved</option>
                <option value="sent">Shipped / Sent</option>
                <option value="received">Received</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase">No. Transaksi</th>
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase">Asal → Tujuan</th>
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase text-center">Status</th>
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase text-center">Tgl Request</th>
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase text-right">Unit</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($distributions as $dist)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.03] transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 dark:text-white block">{{ $dist->distribution_number }}</span>
                                <span class="text-[9px] text-gray-400 dark:text-gray-500 font-bold uppercase">{{ $dist->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-600 dark:text-gray-300">{{ $dist->origin->name }}</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-gray-300"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                                    <span class="font-bold text-brand-600">{{ $dist->destination->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $color = match($dist->status) {
                                        'requested' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20',
                                        'approved' => 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20',
                                        'sent' => 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-500/15 dark:text-blue-400 dark:border-blue-500/20',
                                        'received' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20',
                                        'cancelled' => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20',
                                        default => 'bg-gray-50 text-gray-400 dark:bg-white/[0.03] dark:text-gray-500'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg font-black text-[9px] uppercase border {{ $color }}">
                                    {{ $dist->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 font-medium">
                                {{ $dist->requested_at?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-gray-900 dark:text-white">{{ number_format($dist->total_qty) }}</span>
                                <div class="text-[9px] text-gray-400 dark:text-gray-500 font-bold uppercase">{{ $dist->total_items }} Items</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        $canProcess = $dist->status == 'requested' && 
                                                     auth()->user()->hasAnyRole(['super-admin', 'kepala-farmasi', 'petugas-gudang']);
                                    @endphp

                                    @if($canProcess)
                                        <a href="{{ route('inventory.distributions.process', $dist->id) }}" class="p-2 text-brand-500 hover:bg-brand-50 rounded-lg transition-all" title="Proses Pengiriman">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </a>
                                    @endif
                                    
                                    @php
                                        $canReceive = $dist->status == 'sent' && 
                                                     ($warehouseId == $dist->destination_warehouse_id || auth()->user()->hasAnyRole(['super-admin', 'kepala-farmasi', 'apoteker']));
                                    @endphp

                                    @if($canReceive)
                                        <a href="{{ route('inventory.distributions.receive', $dist->id) }}" class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-all" title="Terima Barang">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </a>
                                    @endif

                                    <button wire:click="showDetail({{ $dist->id }})" class="p-2 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-white/[0.03] rounded-lg transition-all" title="Lihat Detail">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center italic text-gray-300">Belum ada data distribusi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50/30 dark:bg-white/[0.02] border-t border-gray-50 dark:border-gray-800">
            {{ $distributions->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if ($viewingDistribution)
        <div x-data class="fixed inset-0 z-99999 flex items-center justify-center p-4 overflow-y-auto"
            :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div wire:click="closeDetail" class="fixed inset-0 bg-gray-900/40 backdrop-blur-[2px]"></div>

            <div @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Detail Distribusi</p>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mt-0.5">{{ $viewingDistribution->distribution_number }}</h3>
                        <div class="flex items-center gap-2 mt-1.5 text-xs font-bold">
                            <span class="text-gray-600 dark:text-gray-300">{{ $viewingDistribution->origin->name }}</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-gray-300 dark:text-gray-600"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                            <span class="text-brand-600 dark:text-brand-400">{{ $viewingDistribution->destination->name }}</span>
                        </div>
                    </div>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors shrink-0">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <!-- Timeline -->
                    <div class="grid grid-cols-4 gap-2 mb-6">
                        @php
                            $steps = [
                                ['label' => 'Diminta', 'at' => $viewingDistribution->requested_at, 'by' => $viewingDistribution->creator?->name, 'done' => (bool) $viewingDistribution->requested_at],
                                ['label' => 'Disetujui', 'at' => $viewingDistribution->approved_at, 'by' => $viewingDistribution->approver?->name, 'done' => (bool) $viewingDistribution->approved_at],
                                ['label' => 'Dikirim', 'at' => $viewingDistribution->sent_at, 'by' => $viewingDistribution->sender?->name, 'done' => (bool) $viewingDistribution->sent_at],
                                ['label' => 'Diterima', 'at' => $viewingDistribution->received_at, 'by' => $viewingDistribution->receiver?->name, 'done' => (bool) $viewingDistribution->received_at],
                            ];
                        @endphp
                        @foreach ($steps as $step)
                            <div class="text-center">
                                <div class="w-2 h-2 rounded-full mx-auto mb-1.5 {{ $step['done'] ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                <p class="text-[9px] font-black uppercase tracking-wide {{ $step['done'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">{{ $step['label'] }}</p>
                                @if ($step['done'])
                                    <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $step['at']->format('d/m/y H:i') }}</p>
                                    @if ($step['by'])
                                        <p class="text-[9px] text-gray-400 dark:text-gray-500 truncate">{{ $step['by'] }}</p>
                                    @endif
                                @else
                                    <p class="text-[9px] text-gray-300 dark:text-gray-600 mt-0.5">-</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($viewingDistribution->notes)
                        <div class="mb-5 bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-gray-800 rounded-xl p-3">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Catatan</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">{{ $viewingDistribution->notes }}</p>
                        </div>
                    @endif

                    <!-- Items -->
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Daftar Item ({{ $viewingDistribution->details->count() }})</p>
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/[0.03]">
                                    <th class="px-4 py-2.5 font-black text-gray-400 dark:text-gray-500 uppercase text-[9px]">Item</th>
                                    <th class="px-4 py-2.5 font-black text-gray-400 dark:text-gray-500 uppercase text-[9px]">Batch</th>
                                    <th class="px-4 py-2.5 font-black text-gray-400 dark:text-gray-500 uppercase text-[9px] text-right">Diminta</th>
                                    <th class="px-4 py-2.5 font-black text-gray-400 dark:text-gray-500 uppercase text-[9px] text-right">Dikirim</th>
                                    <th class="px-4 py-2.5 font-black text-gray-400 dark:text-gray-500 uppercase text-[9px] text-right">Diterima</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @forelse ($viewingDistribution->details as $detail)
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <span class="font-bold text-gray-800 dark:text-white block">{{ $detail->item->name ?? '-' }}</span>
                                            <span class="text-[9px] text-gray-400 dark:text-gray-500">{{ $detail->item->unit->name ?? '' }}</span>
                                        </td>
                                        <td class="px-4 py-2.5 font-mono text-gray-500 dark:text-gray-400">{{ $detail->batch->batch_number ?? '-' }}</td>
                                        <td class="px-4 py-2.5 text-right font-bold text-gray-700 dark:text-gray-300">{{ number_format($detail->qty_requested) }}</td>
                                        <td class="px-4 py-2.5 text-right font-bold text-gray-700 dark:text-gray-300">{{ $detail->qty_sent !== null ? number_format($detail->qty_sent) : '-' }}</td>
                                        <td class="px-4 py-2.5 text-right font-bold text-emerald-600 dark:text-emerald-400">{{ $detail->qty_received !== null ? number_format($detail->qty_received) : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-300 dark:text-gray-600 italic">Belum ada item.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
