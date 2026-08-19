<div
    x-data="{ open: @entangle('show') }"
    x-show="open"
    x-cloak
    style="display: none"
    class="fixed inset-0 z-[999999] flex items-center justify-center p-4 overflow-y-auto"
    :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }"
>
    <div @click="open = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <div @click.stop
        class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl shadow-indigo-950/10 overflow-hidden"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-3"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        <!-- Header -->
        <div class="px-6 pt-6 pb-6 bg-gradient-to-br from-indigo-600 via-brand-500 to-violet-600 relative overflow-hidden">
            <!-- subtle animated mesh glow, not decorative "blobs" -->
            <div class="absolute inset-0 opacity-40" style="background: radial-gradient(circle at 85% 0%, rgb(255 255 255 / 0.25), transparent 55%);"></div>

            <div class="relative flex items-start gap-3.5">
                <div class="ai-glow-badge w-11 h-11 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4" />
                        <circle cx="12" cy="12" r="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/60">AI Assistant · Ringkasan Harian</p>
                    <h3 class="text-lg font-black text-white leading-tight mt-0.5 truncate">Selamat Datang Kembali!</h3>
                </div>
                <button wire:click="dismiss" class="text-white/60 hover:text-white hover:bg-white/10 rounded-lg p-1 shrink-0 transition-colors">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            @if ($nearExpiredCount > 0 || $criticalStockCount > 0)
                <div class="flex gap-3 mb-4">
                    @if ($nearExpiredCount > 0)
                        <div class="ai-fade-up flex-1 bg-amber-50 dark:bg-amber-500/10 border border-amber-200/70 dark:border-amber-500/20 rounded-xl px-3 py-2.5 transition-transform hover:-translate-y-0.5" style="animation-delay: 60ms">
                            <p class="text-xl font-black text-amber-600 dark:text-amber-400 tabular-nums">{{ $nearExpiredCount }}</p>
                            <p class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wide">Mendekati ED</p>
                        </div>
                    @endif
                    @if ($criticalStockCount > 0)
                        <div class="ai-fade-up flex-1 bg-red-50 dark:bg-red-500/10 border border-red-200/70 dark:border-red-500/20 rounded-xl px-3 py-2.5 transition-transform hover:-translate-y-0.5" style="animation-delay: 120ms">
                            <p class="text-xl font-black text-red-600 dark:text-red-400 tabular-nums">{{ $criticalStockCount }}</p>
                            <p class="text-[10px] font-bold text-red-700 dark:text-red-400 uppercase tracking-wide">Stok Kritis</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="ai-fade-up bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-gray-800 rounded-xl p-4" style="animation-delay: 180ms">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $content }}</p>
            </div>

            @if ($aiGenerated)
                <p class="ai-fade-up mt-3 text-[10px] text-gray-400 dark:text-gray-500 flex items-center gap-1.5" style="animation-delay: 260ms">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                    </span>
                    Dibuat otomatis oleh AI berdasarkan data stok terkini
                </p>
            @endif

            <div class="mt-5 flex gap-3">
                <button wire:click="dismiss"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] active:scale-[0.98] transition-all">
                    Tutup
                </button>
                <a href="/inventory/dashboard" wire:click="dismiss"
                    class="flex-1 text-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-brand-500 hover:shadow-lg hover:shadow-brand-500/30 text-sm font-bold text-white active:scale-[0.98] transition-all">
                    Lihat Dashboard Stok
                </a>
            </div>
        </div>
    </div>
</div>
