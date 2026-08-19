<div
    x-data="{
        open: false,
        selected: 0,
        get flatCount() {
            return $refs.resultsWrap ? $refs.resultsWrap.querySelectorAll('[data-result]').length : 0;
        },
        openPalette() {
            this.open = true;
            this.selected = 0;
            $wire.resetQuery();
            $nextTick(() => $refs.searchInput.focus());
        },
        closePalette() {
            this.open = false;
        },
        move(dir) {
            const count = this.flatCount;
            if (count === 0) return;
            this.selected = (this.selected + dir + count) % count;
            $nextTick(() => {
                const el = $refs.resultsWrap.querySelectorAll('[data-result]')[this.selected];
                el?.scrollIntoView({ block: 'nearest' });
            });
        },
        goSelected() {
            const el = $refs.resultsWrap?.querySelectorAll('[data-result]')[this.selected];
            el?.click();
        }
    }"
    x-init="
        window.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                open ? closePalette() : openPalette();
            } else if (e.key === 'Escape' && open) {
                closePalette();
            }
        });
    "
    @keydown.escape.window="closePalette()"
>
    <!-- Trigger button styled exactly like the old placeholder search bar -->
    <button type="button" @click="openPalette()"
        class="hidden xl:flex items-center gap-2 h-11 w-full max-w-[430px] rounded-lg border border-gray-200 bg-transparent pl-4 pr-2.5 text-sm text-gray-400 shadow-theme-xs transition-colors hover:border-brand-300 dark:border-gray-800 dark:bg-white/3 dark:text-gray-500 dark:hover:border-brand-800">
        <svg class="fill-gray-500 dark:fill-gray-400 shrink-0" width="18" height="18" viewBox="0 0 20 20" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                fill="" />
        </svg>
        <span class="flex-1 text-left">Search or type command...</span>
        <span class="inline-flex items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">
            <span>⌘</span><span>K</span>
        </span>
    </button>

    <!-- Palette overlay -->
    <div x-show="open" x-cloak style="display: none" class="fixed inset-0 z-99999 flex items-start justify-center pt-24 px-4"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click="closePalette()" class="fixed inset-0 bg-gray-900/40 backdrop-blur-[2px]"></div>

        <div @click.stop
            class="relative w-full max-w-xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <!-- Input -->
            <div class="flex items-center gap-3 px-4 border-b border-gray-100 dark:border-gray-800">
                <svg class="fill-gray-400 shrink-0" width="18" height="18" viewBox="0 0 20 20" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" />
                </svg>
                <input x-ref="searchInput" type="text" wire:model.live.debounce.200ms="query"
                    placeholder="Cari menu, item obat, atau supplier..."
                    autocomplete="off"
                    @keydown.arrow-down.prevent="move(1)"
                    @keydown.arrow-up.prevent="move(-1)"
                    @keydown.enter.prevent="goSelected()"
                    class="flex-1 h-13 bg-transparent border-0 text-sm text-gray-800 placeholder:text-gray-400 focus:outline-none focus:ring-0 dark:text-white/90 dark:placeholder:text-gray-500">
                <kbd class="hidden sm:inline text-[10px] font-semibold text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-1.5 py-0.5">ESC</kbd>
            </div>

            <!-- Results -->
            <div x-ref="resultsWrap" class="max-h-96 overflow-y-auto custom-scrollbar py-2">
                @if (empty($this->results['menu']) && empty($this->results['items']) && empty($this->results['suppliers']))
                    <div class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                        Tidak ada hasil untuk "{{ $query }}"
                    </div>
                @endif

                @if (!empty($this->results['menu']))
                    <div class="px-4 pt-2 pb-1 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                        {{ $query === '' ? 'Akses Cepat' : 'Menu & Halaman' }}
                    </div>
                    @foreach ($this->results['menu'] as $m)
                        <a href="{{ $m['path'] }}" data-result
                            x-bind:class="$refs.resultsWrap && Array.from($refs.resultsWrap.querySelectorAll('[data-result]')).indexOf($el) === selected ? 'bg-gray-50 dark:bg-white/[0.05]' : ''"
                            @mouseenter="selected = Array.from($refs.resultsWrap.querySelectorAll('[data-result]')).indexOf($el)"
                            class="flex items-center gap-3 px-4 py-2.5 mx-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                            <span class="text-gray-400 dark:text-gray-500 shrink-0">
                                {!! \App\Helpers\MenuHelper::getIconSvg($m['icon']) !!}
                            </span>
                            <span class="flex-1">{{ $m['name'] }}</span>
                        </a>
                    @endforeach
                @endif

                @if (!empty($this->results['items']))
                    <div class="px-4 pt-3 pb-1 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                        Item Obat & BMHP
                    </div>
                    @foreach ($this->results['items'] as $it)
                        <a href="{{ $it['path'] }}" data-result
                            x-bind:class="$refs.resultsWrap && Array.from($refs.resultsWrap.querySelectorAll('[data-result]')).indexOf($el) === selected ? 'bg-gray-50 dark:bg-white/[0.05]' : ''"
                            @mouseenter="selected = Array.from($refs.resultsWrap.querySelectorAll('[data-result]')).indexOf($el)"
                            class="flex items-center gap-3 px-4 py-2.5 mx-2 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                            <span class="text-gray-400 dark:text-gray-500 shrink-0">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </span>
                            <span class="flex-1 text-gray-700 dark:text-gray-300">{{ $it['name'] }}</span>
                            <span class="text-[10px] font-mono text-gray-400">{{ $it['subtitle'] }}</span>
                        </a>
                    @endforeach
                @endif

                @if (!empty($this->results['suppliers']))
                    <div class="px-4 pt-3 pb-1 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                        Supplier
                    </div>
                    @foreach ($this->results['suppliers'] as $s)
                        <a href="{{ $s['path'] }}" data-result
                            x-bind:class="$refs.resultsWrap && Array.from($refs.resultsWrap.querySelectorAll('[data-result]')).indexOf($el) === selected ? 'bg-gray-50 dark:bg-white/[0.05]' : ''"
                            @mouseenter="selected = Array.from($refs.resultsWrap.querySelectorAll('[data-result]')).indexOf($el)"
                            class="flex items-center gap-3 px-4 py-2.5 mx-2 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors">
                            <span class="text-gray-400 dark:text-gray-500 shrink-0">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </span>
                            <span class="flex-1 text-gray-700 dark:text-gray-300">{{ $s['name'] }}</span>
                            <span class="text-[10px] font-mono text-gray-400">{{ $s['subtitle'] }}</span>
                        </a>
                    @endforeach
                @endif
            </div>

            <!-- Footer hint -->
            <div class="flex items-center gap-4 px-4 py-2.5 border-t border-gray-100 dark:border-gray-800 text-[10px] text-gray-400 dark:text-gray-500">
                <span class="inline-flex items-center gap-1"><kbd class="border border-gray-200 dark:border-gray-700 rounded px-1">↑↓</kbd> navigasi</span>
                <span class="inline-flex items-center gap-1"><kbd class="border border-gray-200 dark:border-gray-700 rounded px-1">Enter</kbd> pilih</span>
                <span class="inline-flex items-center gap-1"><kbd class="border border-gray-200 dark:border-gray-700 rounded px-1">Esc</kbd> tutup</span>
            </div>
        </div>
    </div>
</div>
