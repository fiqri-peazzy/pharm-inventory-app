<div class="space-y-4" wire:poll.30s>
    <!-- Header Context -->
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-gray-100 shadow-sm dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 @if ($isGlobal) bg-brand-500 @else bg-amber-500 @endif rounded-xl flex items-center justify-center text-white shadow-lg transition-all">
                @if ($isGlobal)
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                @else
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                @endif
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight leading-none dark:text-white">
                    {{ $isGlobal ? 'Monitoring Stok Global' : 'Monitoring Stok: ' . $warehouse->name }}
                </h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 block dark:text-gray-500">Data Real-time &
                    Analitik Inventaris</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if (
                !$isGlobal &&
                    auth()->user()->hasAnyRole(['super-admin', 'kepala-farmasi', 'direktur']))
                <a href="{{ route('inventory.dashboard') }}"
                    class="px-4 py-2 bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:bg-brand-50 hover:text-brand-600 rounded-xl transition-all flex items-center gap-2 border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-400">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="3">
                        <path d="M19 12H5"></path>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Semua Gudang
                </a>
            @endif
            <button wire:click="loadData" class="p-2 text-gray-400 hover:text-brand-500 transition-colors dark:text-gray-500">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <path d="M23 4v6h-6"></path>
                    <path d="M1 20v-6h6"></path>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Stats Cards: More Compact -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Asset -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm group dark:bg-white/[0.03] dark:border-gray-800">
            <div class="flex justify-between items-center mb-2">
                <div
                    class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-black text-[11px] dark:bg-indigo-500/15 dark:text-indigo-400">
                    Rp
                </div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Total Aset</span>
            </div>
            <div class="text-lg font-black text-gray-900 tracking-tight dark:text-white">
                Rp {{ number_format($summary['total_value'], 0, ',', '.') }}
            </div>
        </div>

        <!-- Quantity -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm group dark:bg-white/[0.03] dark:border-gray-800">
            <div class="flex justify-between items-center mb-2">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center dark:bg-emerald-500/15 dark:text-emerald-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                    </svg>
                </div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Jumlah Stok</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span
                    class="text-xl font-black text-gray-900 tracking-tight dark:text-white">{{ number_format($summary['total_qty']) }}</span>
                <span class="text-[9px] font-bold text-gray-400 uppercase dark:text-gray-500">Unit</span>
            </div>
        </div>

        <!-- Low Stock -->
        <div
            class="bg-white p-4 rounded-2xl border @if ($summary['low_stock_count'] > 0) border-red-100 bg-red-50/5 @else border-gray-100 @endif shadow-sm group dark:bg-white/[0.03] dark:border-gray-800">
            <div class="flex justify-between items-center mb-2">
                <div
                    class="w-8 h-8 @if ($summary['low_stock_count'] > 0) bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-400 @else bg-gray-50 text-gray-400 dark:bg-white/[0.03] dark:text-gray-500 @endif rounded-lg flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path
                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                        </path>
                    </svg>
                </div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Stok Kritis</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span
                    class="text-xl font-black @if ($summary['low_stock_count'] > 0) text-red-600 @else text-gray-900 @endif tracking-tight dark:text-white">{{ number_format($summary['low_stock_count']) }}</span>
                <span class="text-[9px] font-bold text-gray-400 uppercase dark:text-gray-500">Item</span>
            </div>
        </div>

        <!-- Expiry -->
        <div
            class="bg-white p-4 rounded-2xl border @if ($summary['near_expired_count'] > 0) border-amber-100 bg-amber-50/5 @else border-gray-100 @endif shadow-sm group dark:bg-white/[0.03] dark:border-gray-800">
            <div class="flex justify-between items-center mb-2">
                <div
                    class="w-8 h-8 @if ($summary['near_expired_count'] > 0) bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400 @else bg-gray-50 text-gray-400 dark:bg-white/[0.03] dark:text-gray-500 @endif rounded-lg flex items-center justify-center">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-500">Kadaluarsa</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span
                    class="text-xl font-black @if ($summary['near_expired_count'] > 0) text-amber-600 @else text-gray-900 @endif tracking-tight dark:text-white">{{ number_format($summary['near_expired_count']) }}</span>
                <span class="text-[9px] font-bold text-gray-400 uppercase dark:text-gray-500">Batch</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

        <!-- Left Column: Tables (8/12) -->
        <div class="xl:col-span-8 space-y-4">

            <!-- Table: Aktivitas Stok Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30 dark:bg-white/[0.02] dark:border-gray-800">
                    <h3 class="text-[11px] font-black uppercase tracking-widest text-gray-600 flex items-center gap-2 dark:text-gray-300">
                        <span class="w-1.5 h-4 bg-brand-500 rounded-full"></span>
                        Aktivitas Stok Terbaru (Masuk & Keluar)
                    </h3>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter dark:text-gray-500">15 Transaksi
                        Terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[11px]">
                        <thead>
                            <tr class="bg-white border-b border-gray-50 dark:bg-white/[0.03] dark:border-gray-800">
                                <th class="px-6 py-3 font-black text-gray-400 uppercase dark:text-gray-500">Waktu</th>
                                <th class="px-6 py-3 font-black text-gray-400 uppercase dark:text-gray-500">Barang</th>
                                <th class="px-6 py-3 font-black text-gray-400 uppercase dark:text-gray-500">Gudang</th>
                                <th class="px-6 py-3 font-black text-gray-400 uppercase text-center dark:text-gray-500">Tipe</th>
                                <th class="px-6 py-3 font-black text-gray-400 uppercase text-right dark:text-gray-500">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($recentActivities as $act)
                                <tr class="hover:bg-gray-50/50 transition-colors dark:hover:bg-white/[0.03]">
                                    <td class="px-6 py-3 text-gray-400 whitespace-nowrap dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($act->transaction_date)->translatedFormat('d M H:i') }}
                                    </td>
                                    <td class="px-6 py-3 font-bold text-gray-900 dark:text-white">
                                        {{ $act->item->name }}
                                        <div
                                            class="text-[9px] text-gray-400 font-normal uppercase italic tracking-tighter dark:text-gray-500">
                                            Batch: {{ $act->batch->batch_number ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $act->warehouse->name }}</td>
                                    <td class="px-6 py-3 text-center">
                                        @if ($act->qty_in > 0)
                                            <span
                                                class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-md font-black uppercase text-[8px] border border-emerald-100 italic dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20">MASUK</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 bg-amber-50 text-amber-600 rounded-md font-black uppercase text-[8px] border border-amber-100 italic dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20">KELUAR</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-3 text-right font-black {{ $act->qty_in > 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ number_format(max($act->qty_in, $act->qty_out)) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center italic text-gray-300">Belum ada
                                        aktivitas
                                        stok tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Table: Stok Kritis -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col dark:bg-white/[0.03] dark:border-gray-800">
                    <div class="px-6 py-3 border-b border-gray-50 bg-red-50/20 flex items-center justify-between dark:border-gray-800 dark:bg-red-500/5">
                        <h3
                            class="text-[10px] font-black uppercase tracking-widest text-red-600 flex items-center gap-2">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="3">
                                <path
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            Stok Kritis (< Min) </h3>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-[10px]">
                            <thead>
                                <tr class="bg-gray-50/30 dark:bg-white/[0.02]">
                                    <th class="px-4 py-2 text-gray-400 font-black uppercase text-center dark:text-gray-500">Info Barang
                                    </th>
                                    <th class="px-4 py-2 text-gray-400 font-black uppercase text-center dark:text-gray-500">Stok</th>
                                    <th class="px-4 py-2 text-gray-400 font-black uppercase text-center dark:text-gray-500">RP & Min</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @forelse($lowStockItems as $item)
                                    <tr class="hover:bg-red-50/5 transition-colors dark:hover:bg-red-500/5">
                                        <td class="px-4 py-2">
                                            <span
                                                class="font-bold text-gray-900 leading-tight block dark:text-white">{{ $item->name }}</span>
                                            @if ($isGlobal)
                                                <div class="text-[8px] text-brand-500 font-black uppercase italic">
                                                    {{ $item->alert_warehouse }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span
                                                class="text-sm font-black text-red-600 tracking-tighter">{{ number_format($item->current_stock) }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="text-[9px] font-black text-amber-500 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100 mb-0.5 dark:bg-amber-500/15 dark:border-amber-500/20 dark:text-amber-400">RP:
                                                    {{ number_format($item->alert_reorder_point) }}</span>
                                                <span class="text-[8px] font-bold text-gray-400 dark:text-gray-500">MIN:
                                                    {{ number_format($item->alert_min_stock) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center italic text-gray-300">Stok
                                            semua aman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Table: Hampir Kadaluarsa -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col dark:bg-white/[0.03] dark:border-gray-800">
                    <div class="px-6 py-3 border-b border-gray-50 bg-amber-50/20 flex items-center justify-between dark:border-gray-800 dark:bg-amber-500/5">
                        <h3
                            class="text-[10px] font-black uppercase tracking-widest text-amber-600 flex items-center gap-2">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="3">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            Monitoring Kadaluarsa
                        </h3>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-[10px]">
                            <thead>
                                <tr class="bg-gray-50/30 dark:bg-white/[0.02]">
                                    <th class="px-4 py-2 text-gray-400 font-black uppercase dark:text-gray-500">Barang/Batch</th>
                                    <th class="px-4 py-2 text-gray-400 font-black uppercase text-center dark:text-gray-500">Tgl ED</th>
                                    <th class="px-4 py-2 text-gray-400 font-black uppercase text-right dark:text-gray-500">Sisa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                @forelse($nearExpiredItems as $batch)
                                    <tr class="hover:bg-amber-50/5 transition-colors dark:hover:bg-amber-500/5">
                                        <td class="px-4 py-2">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $batch->item->name }}</span>
                                            <div class="text-[8px] text-gray-400 uppercase tracking-tighter dark:text-gray-500">B:
                                                {{ $batch->batch_number }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @php $isPast = $batch->expired_date->isPast(); @endphp
                                            <span
                                                class="px-2 py-0.5 rounded-md font-black {{ $isPast ? 'bg-red-500 text-white shadow-sm' : 'bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/15 dark:border-amber-500/20 dark:text-amber-400' }}">
                                                {{ $batch->expired_date->format('d/m/y') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right font-black text-gray-600 dark:text-gray-300">
                                            {{ number_format($batch->current_qty) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center italic text-gray-300">Aman
                                            untuk 6
                                            bulan ke depan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Charts & Node List (4/12) -->
        <div class="xl:col-span-4 space-y-4">

            <!-- Chart: Distribusi Nilai Stok -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-fit dark:bg-white/[0.03] dark:border-gray-800">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6 dark:text-gray-500">Distribusi Nilai Stok
                    per Kategori</h3>
                <div id="distributionChart" class="min-h-[280px]" wire:ignore></div>
            </div>

            <!-- Chart: Tren Distribusi 7 Hari Terakhir -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-fit dark:bg-white/[0.03] dark:border-gray-800">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6 dark:text-gray-500">Tren Distribusi
                    Barang (7 Hari Terakhir)</h3>
                <div id="trendChart" class="min-h-[200px]" wire:ignore></div>
            </div>

            <!-- Global Only: Daftar Gudang -->
            @if ($isGlobal)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-fit dark:bg-white/[0.03] dark:border-gray-800">
                    <div class="px-6 py-3 border-b border-gray-50 flex items-center justify-between dark:border-gray-800">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300">Sebaran per
                            Gudang/Depo
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto custom-scrollbar dark:divide-gray-800">
                        @foreach ($warehouseStock as $wh)
                            <a href="{{ route('inventory.dashboard', ['warehouse' => $wh['id']]) }}"
                                class="p-4 block hover:bg-gray-50 transition-colors group dark:hover:bg-white/[0.03]">
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="text-xs font-black text-gray-900 group-hover:text-brand-600 dark:text-white">{{ $wh['name'] }}</span>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter italic dark:text-gray-500">
                                        Rp {{ number_format($wh['total_value'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden dark:bg-gray-800">
                                    @php $ratio = $summary['total_value'] > 0 ? min(100, ($wh['total_value'] / $summary['total_value']) * 100) : 0; @endphp
                                    <div class="bg-brand-500 h-full rounded-full transition-all duration-1000 shadow-sm"
                                        style="width:{{ $ratio }}%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span
                                        class="text-[9px] font-black text-brand-500 uppercase italic">{{ number_format($wh['total_qty']) }}
                                        Unit Stok</span>
                                    @if ($wh['low_stock_alerts'] > 0)
                                        <span
                                            class="px-2 py-0.5 bg-red-50 text-red-600 rounded font-black text-[8px] border border-red-100 dark:bg-red-500/15 dark:border-red-500/20 dark:text-red-400">{{ $wh['low_stock_alerts'] }}
                                            Stok Kritis</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Abstract Analytics Card -->
            <div class="bg-gray-900 rounded-3xl p-6 relative overflow-hidden shadow-xl shadow-gray-200 group">
                <div class="relative z-10">
                    <h4 class="text-white/40 text-[9px] font-black uppercase tracking-[0.2em] mb-3">Health Ratio</h4>
                    <div class="flex items-baseline gap-2 mb-4">
                        <span class="text-white text-3xl font-black italic tracking-tighter">{{ number_format($healthRatio, 1) }}%</span>
                        <span
                            class="text-emerald-400 text-[10px] font-bold uppercase tracking-widest italic animate-pulse">{{ $healthStatus }}</span>
                    </div>
                    <p class="text-[9px] text-white/50 leading-relaxed font-medium uppercase tracking-[0.1em]">Sistem
                        otomatis memantau ketersediaan barang di seluruh instalasi farmasi secara real-time.</p>
                </div>
                <div
                    class="absolute -right-8 -top-8 w-32 h-32 bg-brand-500/20 rounded-full blur-3xl group-hover:bg-brand-500/30 transition-all duration-1000">
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('livewire:navigated', () => {
                initDashboardCharts();
            });
            document.addEventListener('livewire:initialized', () => {
                initDashboardCharts();
            });

            // Re-render charts with correct theme colors when the user toggles dark mode.
            let __dashboardCharts = [];
            const __darkModeObserver = new MutationObserver((mutations) => {
                for (const m of mutations) {
                    if (m.attributeName === 'class') {
                        initDashboardCharts();
                        break;
                    }
                }
            });
            __darkModeObserver.observe(document.documentElement, { attributes: true });

            function isDarkMode() {
                return document.documentElement.classList.contains('dark');
            }

            function initDashboardCharts() {
                // Destroy any previously rendered chart instances before re-rendering.
                __dashboardCharts.forEach(c => {
                    try { c.destroy(); } catch (e) {}
                });
                __dashboardCharts = [];

                const dark = isDarkMode();
                const mutedColor = dark ? '#9ca3af' : '#94a3b8';
                const strongColor = dark ? '#f9fafb' : '#1e293b';

                const chartData = @json($distributionChart);
                if (!chartData || chartData.length === 0) return;
                const chartEl = document.querySelector("#distributionChart");
                if (!chartEl) return;
                chartEl.innerHTML = '';

                const options = {
                    series: chartData.map(d => parseFloat(d.value)),
                    labels: chartData.map(d => d.name),
                    chart: {
                        type: 'donut',
                        height: 320,
                        fontFamily: 'Inter, sans-serif',
                        background: 'transparent',
                    },
                    theme: {
                        mode: dark ? 'dark' : 'light'
                    },
                    colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#3b82f6', '#ec4899'],
                    legend: {
                        position: 'bottom',
                        fontSize: '9px',
                        fontWeight: 900,
                        fontFamily: 'Inter, sans-serif',
                        textTransform: 'uppercase',
                        labels: {
                            colors: mutedColor
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '80%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '9px',
                                        fontWeight: 900,
                                        color: mutedColor,
                                        offsetY: -5
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '14px',
                                        fontWeight: 900,
                                        color: strongColor,
                                        offsetY: 5,
                                        formatter: function(val) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(val));
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'TOTAL ASET',
                                        fontSize: '9px',
                                        fontWeight: 900,
                                        color: mutedColor,
                                        formatter: function(w) {
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(total));
                                        }
                                    }
                                }
                            }
                        }
                    },
                    stroke: {
                        width: 0,
                        colors: dark ? ['#1f2937'] : ['#ffffff']
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        theme: dark ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(val));
                            }
                        }
                    }
                };
                const distChart = new ApexCharts(chartEl, options);
                distChart.render();
                __dashboardCharts.push(distChart);

                const trendData = @json($trendChart);
                const trendEl = document.querySelector("#trendChart");
                if (trendEl && trendData && trendData.series) {
                    trendEl.innerHTML = '';
                    const trendOptions = {
                        series: [{
                            name: 'Barang Keluar',
                            data: trendData.series
                        }],
                        chart: {
                            type: 'line',
                            height: 200,
                            fontFamily: 'Inter, sans-serif',
                            background: 'transparent',
                            toolbar: {
                                show: false
                            }
                        },
                        theme: {
                            mode: dark ? 'dark' : 'light'
                        },
                        colors: ['#6366f1'],
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        xaxis: {
                            categories: trendData.categories,
                            labels: {
                                style: {
                                    fontSize: '9px',
                                    fontWeight: 700,
                                    colors: mutedColor
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '9px',
                                    fontWeight: 700,
                                    colors: mutedColor
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        grid: {
                            strokeDashArray: 4,
                            borderColor: dark ? '#374151' : '#e5e7eb'
                        },
                        tooltip: {
                            theme: dark ? 'dark' : 'light',
                            y: {
                                formatter: function(val) {
                                    return new Intl.NumberFormat('id-ID').format(val) + ' unit';
                                }
                            }
                        }
                    };
                    const trendChartInstance = new ApexCharts(trendEl, trendOptions);
                    trendChartInstance.render();
                    __dashboardCharts.push(trendChartInstance);
                }
            }
        </script>
    @endpush
</div>
