<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <!-- Notification Button -->
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
    >
        @if (count($items) > 0)
            <span class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400">
                <span class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping"></span>
            </span>
        @endif

        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z" fill="" />
        </svg>
    </button>

    <!-- Dropdown -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-[17px] flex w-[360px] flex-col rounded-2xl border border-gray-200 bg-white shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
            <h5 class="text-sm font-black text-gray-800 dark:text-white">Notifikasi</h5>
            <button @click="closeDropdown()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="max-h-80 overflow-y-auto custom-scrollbar">
            @forelse ($items as $item)
                <a href="{{ $item['url'] }}" @click="closeDropdown()"
                    class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors">
                    @php
                        $colorMap = [
                            'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
                            'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',
                            'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400',
                            'red' => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400',
                        ];
                        $iconSvgs = [
                            'file-text' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
                            'package' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>',
                            'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>',
                            'alert-triangle' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
                        ];
                    @endphp
                    <span class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 {{ $colorMap[$item['color']] ?? $colorMap['blue'] }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $iconSvgs[$item['icon']] ?? $iconSvgs['file-text'] !!}</svg>
                    </span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-xs font-bold text-gray-700 dark:text-gray-300 leading-snug">{{ $item['title'] }}</span>
                        <span class="block text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $item['subtitle'] }}</span>
                        <span class="block text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ \Illuminate\Support\Carbon::parse($item['time'])->diffForHumans() }}</span>
                    </span>
                </a>
            @empty
                <div class="px-4 py-10 text-center">
                    <svg class="mx-auto text-gray-300 dark:text-gray-700 mb-2" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Tidak ada notifikasi baru.</p>
                    <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-0.5">Semua tugas Anda sudah beres.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
