<div>
    <div class="mb-6 flex items-center justify-between">
        <div class="relative w-full md:w-80">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. PR..."
                class="w-full rounded-xl border border-gray-100 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 transition-all shadow-sm">
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg">
            <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-700 dark:text-indigo-400">Menunggu
                Review: {{ $pendingCount }}</span>
        </div>
    </div>

    <div
        class="overflow-hidden bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <table class="w-full text-left text-sm">
            <thead
                class="bg-gray-50/50 text-xs uppercase font-black text-gray-500 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                <tr>
                    <th class="px-6 py-4">Dokumen</th>
                    <th class="px-6 py-4">Gudang & Pemohon</th>
                    <th class="px-6 py-4 text-center">Periode</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($requests as $request)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span
                                    class="font-bold text-gray-900 dark:text-white uppercase tracking-tight">{{ $request->request_number }}</span>
                                <span
                                    class="text-[10px] text-gray-500 dark:text-gray-400 italic">{{ $request->request_date->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span
                                    class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $request->warehouse->name }}</span>
                                <span class="text-[10px] text-gray-400">Oleh:
                                    {{ $request->creator->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">
                                {{ \Carbon\Carbon::createFromDate($request->period_year, (int) $request->period_month, 1)->translatedFormat('M') }}
                                {{ $request->period_year }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'submitted' => 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-500/15 dark:text-blue-400 dark:ring-blue-500/20',
                                    'approved' => 'bg-green-50 text-green-700 ring-green-700/10 dark:bg-green-500/15 dark:text-green-400 dark:ring-green-500/20',
                                    'rejected' => 'bg-red-50 text-red-700 ring-red-700/10 dark:bg-red-500/15 dark:text-red-400 dark:ring-red-500/20',
                                ];
                            @endphp
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-widest {{ $statusColors[$request->status] ?? 'bg-gray-50 text-gray-700 dark:bg-white/[0.05] dark:text-gray-400' }} ring-1 ring-inset">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="selectPr({{ $request->id }})"
                                class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white hover:bg-gray-800 transition-all dark:bg-brand-500 dark:hover:bg-brand-600 shadow-lg shadow-gray-900/10 min-w-24 justify-center">
                                Review Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center opacity-40 italic text-gray-400 text-sm">
                            Tidak ada pengajuan yang membutuhkan tindakan saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>

    <!-- Approval Modal -->
    <div x-data="{ open: @entangle('showApprovalModal') }" x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed inset-0 z-[9999] overflow-hidden" style="display: none;">
        <div class="flex h-screen items-center justify-center p-4 sm:p-6" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div class="fixed inset-0 bg-gray-900/35 backdrop-blur-[2px]" @click="open = false"></div>

            <div
                class="relative w-full h-[90vh] sm:h-auto sm:max-w-4xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden flex flex-col z-[10000]">
                @if ($selectedPr)
                    <!-- Header -->
                    <div
                        class="flex-shrink-0 p-4 sm:p-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div>
                            <h3
                                class="text-base sm:text-lg font-black uppercase text-gray-900 dark:text-white tracking-tighter">
                                Review Purchase Request</h3>
                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-1">
                                {{ $selectedPr->request_number }} • {{ $selectedPr->warehouse->name }}</p>
                        </div>
                        <button @click="open = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex-shrink-0 ml-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M18 6L6 18M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <div class="p-4 sm:p-6">
                            <!-- Items Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs sm:text-sm whitespace-nowrap">
                                    <thead
                                        class="bg-gray-50/50 dark:bg-white/[0.02] text-[10px] uppercase font-black text-gray-400 border-b border-gray-100 dark:border-gray-800 sticky top-0">
                                        <tr>
                                            <th class="px-3 sm:px-4 py-3">Nama Barang</th>
                                            <th class="px-3 sm:px-4 py-3 text-center">Stok Riil</th>
                                            <th class="px-3 sm:px-4 py-3 text-center hidden sm:table-cell">Rata2 Pakai
                                            </th>
                                            <th class="px-3 sm:px-4 py-3 text-center">Qty Minta</th>
                                            <th class="px-3 sm:px-4 py-3 hidden sm:table-cell">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        @foreach ($selectedPr->details as $detail)
                                            <tr
                                                class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                                <td class="px-3 sm:px-4 py-3 sm:py-4">
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="font-bold text-gray-900 dark:text-white text-xs">{{ $detail->item->name }}</span>
                                                        <span
                                                            class="text-[10px] font-mono text-gray-400 uppercase">{{ $detail->item->code }}</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-3 sm:px-4 py-3 sm:py-4 text-center font-mono font-bold text-xs">
                                                    {{ number_format($detail->current_stock) }}</td>
                                                <td
                                                    class="px-3 sm:px-4 py-3 sm:py-4 text-center font-mono text-xs hidden sm:table-cell">
                                                    {{ number_format($detail->average_usage) }}</td>
                                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center">
                                                    <span
                                                        class="rounded-lg bg-indigo-50 dark:bg-indigo-500/10 px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-black text-indigo-700 dark:text-indigo-400 ring-1 ring-inset ring-indigo-700/10 dark:ring-indigo-400/20">{{ number_format($detail->requested_qty) }}
                                                        {{ $detail->item->unit?->name }}</span>
                                                </td>
                                                <td
                                                    class="px-3 sm:px-4 py-3 sm:py-4 text-[10px] text-gray-500 dark:text-gray-400 italic hidden sm:table-cell">
                                                    {{ $detail->notes ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Notes Section -->
                            @if ($selectedPr->notes)
                                <div
                                    class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                    <label
                                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2 block">Notes
                                        dari Pemohon:</label>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $selectedPr->notes }}</p>
                                </div>
                            @endif

                            <!-- Rejection Reason -->
                            @if ($selectedPr->status === 'submitted')
                                <div class="mt-6">
                                    <label
                                        class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-2 block">Catatan
                                        Penolakan (Wajib jika menolak):</label>
                                    <textarea wire:model="rejectionReason" rows="3" placeholder="Tuliskan alasan jika pengajuan ditolak..."
                                        class="w-full rounded-xl border bg-white dark:bg-gray-800 p-3 sm:p-4 text-sm outline-none transition-all
                                        @error('rejectionReason') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-red-500 dark:border-gray-700 dark:focus:border-red-500 @enderror"></textarea>
                                    @error('rejectionReason')
                                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="flex-shrink-0 p-4 sm:p-6 bg-gray-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        @if ($selectedPr->status === 'submitted')
                            <button wire:click="reject"
                                class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 rounded-xl border border-red-200 dark:border-red-500/30 bg-white dark:bg-transparent text-xs font-black uppercase tracking-widest text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all shadow-sm">
                                Tolak Pengajuan
                            </button>
                            <button wire:click="approve"
                                class="w-full sm:w-auto px-6 sm:px-8 py-2.5 sm:py-3 rounded-xl bg-green-600 hover:bg-green-700 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-green-600/20 transition-all">
                                Setujui & Approve
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
