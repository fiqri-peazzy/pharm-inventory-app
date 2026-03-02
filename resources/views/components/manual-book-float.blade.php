<div x-data="{
    open: false,
    showModal: false,
    openPreview() {
        this.showModal = true;
        this.open = false;
    },
    closeModal() {
        this.showModal = false;
    }
}" class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-2">
    {{-- Expand Panel: Download + Preview --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2" class="flex flex-col gap-2 mb-1" style="display:none">
        {{-- Preview Button --}}
        <button @click="openPreview()"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl shadow-lg text-sm font-semibold text-white
                   bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800
                   transition-all duration-200 group whitespace-nowrap">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Lihat PDF
        </button>

        {{-- Download Button --}}
        <a href="{{ route('manual-book.download') }}" target="_blank"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl shadow-lg text-sm font-semibold text-white
                   bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700
                   transition-all duration-200 whitespace-nowrap">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Unduh PDF
        </a>
    </div>

    {{-- FAB Button --}}
    <div class="relative group">
        {{-- Tooltip --}}
        <div
            class="absolute bottom-full right-0 mb-2 px-2.5 py-1 bg-gray-800 text-white text-xs rounded-lg
                    opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none shadow">
            Manual Book
        </div>

        <button @click="open = !open"
            class="w-14 h-14 rounded-full shadow-xl flex items-center justify-center
                   bg-gradient-to-br from-blue-600 to-indigo-700
                   hover:from-blue-700 hover:to-indigo-800
                   text-white transition-all duration-300
                   ring-4 ring-blue-200 dark:ring-blue-900
                   hover:scale-110 active:scale-95"
            :class="{ 'rotate-45': open }" style="transition: transform 0.3s ease;" title="Manual Book">
            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Modal Preview --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="closeModal()"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none">
        {{-- Backdrop --}}
        <div @click="closeModal()" class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm"></div>

        {{-- Modal Container --}}
        <div
            class="relative w-full max-w-5xl h-[90vh] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            {{-- Modal Header --}}
            <div
                class="flex items-center justify-between px-5 py-3.5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-700 to-indigo-700">
                <div class="flex items-center gap-2.5 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="font-semibold text-sm">Manual Book — Medivault Sistem Farmasi RSUD Bumi Panua</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('manual-book.download') }}" target="_blank"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-medium transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Unduh
                    </a>
                    <button @click="closeModal()"
                        class="flex items-center justify-center w-7 h-7 rounded-full bg-white/20 hover:bg-white/30 text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- iFrame PDF --}}
            <iframe x-show="showModal" src="{{ route('manual-book.view') }}" class="flex-1 w-full border-0"
                title="Preview Manual Book"></iframe>
        </div>
    </div>
</div>
