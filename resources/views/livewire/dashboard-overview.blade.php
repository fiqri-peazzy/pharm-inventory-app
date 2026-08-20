<div class="space-y-6">
    <!-- AI Insight -->
    <div class="ai-fade-up rounded-2xl bg-gradient-to-br from-indigo-600 via-brand-500 to-violet-600 p-5 relative overflow-hidden">
        <div class="absolute inset-0 opacity-40" style="background: radial-gradient(circle at 90% 0%, rgb(255 255 255 / 0.25), transparent 55%);"></div>
        <div class="relative flex items-start gap-3.5">
            <div class="ai-glow-badge w-10 h-10 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4" />
                    <circle cx="12" cy="12" r="4" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-white/60 mb-1">AI Insight · Ringkasan Operasional</p>
                @if ($aiInsight)
                    <p class="text-sm font-semibold text-white leading-relaxed">{{ $aiInsight }}</p>
                @else
                    <p class="text-sm font-semibold text-white/70 italic">AI sedang tidak tersedia. Lihat kartu ringkasan di bawah.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-white/[0.03] p-5 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/15 flex items-center justify-center text-brand-600 dark:text-brand-400 mb-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ number_format($metrics['total_items']) }}</p>
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide mt-0.5">Total Item Aktif</p>
        </div>
        <div class="bg-white dark:bg-white/[0.03] p-5 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">Rp{{ number_format($metrics['total_stock_value'], 0, ',', '.') }}</p>
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide mt-0.5">Nilai Stok Saat Ini</p>
        </div>
        <div class="bg-white dark:bg-white/[0.03] p-5 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M10 15l2 2 4-4"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ number_format($metrics['prescriptions_today']) }}</p>
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide mt-0.5">Resep Hari Ini</p>
        </div>
        <div class="bg-white dark:bg-white/[0.03] p-5 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/15 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white tabular-nums">{{ number_format($metrics['pending_approvals']) }}</p>
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wide mt-0.5">Menunggu Persetujuan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Trend chart -->
        <div class="xl:col-span-2 bg-white dark:bg-white/[0.03] p-5 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-black text-gray-800 dark:text-white mb-4">Tren Aktivitas 7 Hari Terakhir</p>
            <div id="dashboardTrendChart"></div>
        </div>

        <!-- Recent activity -->
        <div class="bg-white dark:bg-white/[0.03] p-5 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-black text-gray-800 dark:text-white mb-4">Aktivitas Terbaru</p>
            <div class="space-y-3 max-h-72 overflow-y-auto custom-scrollbar">
                @forelse ($activities as $act)
                    <div class="flex items-start gap-3">
                        @php
                            $dot = ['emerald' => 'bg-emerald-500', 'blue' => 'bg-blue-500', 'indigo' => 'bg-indigo-500'][$act['color']] ?? 'bg-gray-400';
                        @endphp
                        <span class="w-2 h-2 rounded-full {{ $dot }} mt-1.5 shrink-0"></span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate">{{ $act['title'] }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $act['subtitle'] }} · {{ \Illuminate\Support\Carbon::parse($act['time'])->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 dark:text-gray-500 italic text-center py-8">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                let chartInstance = null;

                async function initTrendChart() {
                    const el = document.querySelector('#dashboardTrendChart');
                    if (!el) return;

                    if (!window.ApexCharts) {
                        const mod = await import('apexcharts');
                        window.ApexCharts = mod.default;
                    }

                    if (chartInstance) {
                        try { chartInstance.destroy(); } catch (e) {}
                    }

                    const dark = document.documentElement.classList.contains('dark');
                    const muted = dark ? '#9ca3af' : '#94a3b8';

                    chartInstance = new ApexCharts(el, {
                        series: [
                            { name: 'Resep', data: @json($trend['prescriptions']) },
                            { name: 'Distribusi', data: @json($trend['distributions']) },
                        ],
                        chart: { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
                        theme: { mode: dark ? 'dark' : 'light' },
                        colors: ['#6366f1', '#10b981'],
                        stroke: { curve: 'smooth', width: 2.5 },
                        fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
                        dataLabels: { enabled: false },
                        xaxis: { categories: @json($trend['categories']), labels: { style: { colors: muted, fontSize: '10px' } } },
                        yaxis: { labels: { style: { colors: muted, fontSize: '10px' } } },
                        grid: { strokeDashArray: 4, borderColor: dark ? '#374151' : '#e5e7eb' },
                        legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px', labels: { colors: muted } },
                    });
                    chartInstance.render();
                }

                document.addEventListener('livewire:navigated', initTrendChart);
                new MutationObserver((m) => {
                    for (const x of m) if (x.attributeName === 'class') { initTrendChart(); break; }
                }).observe(document.documentElement, { attributes: true });
            })();
        </script>
    @endpush
</div>
