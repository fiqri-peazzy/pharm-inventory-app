<div>
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between mb-6">
        <div class="relative w-full md:w-80">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.5 17.5L13.875 13.875M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari unit layanan..." class="w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
        </div>
        
        <button wire:click="create" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tambah Unit Layanan
        </button>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-white/[0.02] dark:text-gray-400 border-b border-gray-100 dark:border-gray-800">
                <tr>
                    <th class="px-6 py-4">Unit Layanan</th>
                    <th class="px-6 py-4">Tipe & Kategori</th>
                    <th class="px-6 py-4">Depo Pelayan</th>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($serviceUnits as $unit)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $unit->name }}</span>
                                <span class="text-[11px] font-mono tracking-wider text-indigo-600 dark:text-indigo-400 capitalize">{{ $unit->code }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex w-fit items-center rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">{{ strtoupper($unit->type) }}</span>
                                <span class="text-xs text-gray-500 italic">{{ str_replace('_', ' ', $unit->category) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($unit->defaultWarehouse)
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $unit->defaultWarehouse->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Belum di-map</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col text-xs text-gray-600 dark:text-gray-400">
                                <span>{{ $unit->building }}</span>
                                <span class="text-[10px] text-gray-400">Lantai {{ $unit->floor }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($unit->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 uppercase">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $unit->id }})" class="p-1.5 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-lg transition-colors">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.1667 3.33333C14.3855 3.11451 14.6453 2.94095 14.9312 2.82255C15.2171 2.70414 15.5235 2.6432 15.8333 2.6432C16.1432 2.6432 16.4496 2.70414 16.7355 2.82255C17.0214 2.94095 17.2812 3.11451 17.5 3.33333C17.7188 3.55216 17.8924 3.8119 18.0108 4.0978C18.1292 4.3837 18.1901 4.69013 18.1901 5C18.1901 5.30987 18.1292 5.6163 18.0108 5.9022C17.8924 6.1881 17.7188 6.44784 17.5 6.66667L6.66667 17.5L2.5 18.3333L3.33333 14.1667L14.1667 3.33333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button wire:click="$dispatch('confirm-delete', { id: {{ $unit->id }}, action: 'delete-unit' })" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8333 5.83333L15.1111 15.9444C15.0483 16.8234 14.3164 17.5 13.4355 17.5H6.56447C5.68357 17.5 4.9517 16.8234 4.88889 15.9444L4.16667 5.83333M8.33333 9.16667V14.1667M11.6667 9.16667V14.1667M13.3333 5.83333V4.16667C13.3333 3.24619 12.5871 2.5 11.6667 2.5H8.33333C7.41286 2.5 6.66667 4.16667V5.83333M3.33333 5.83333H16.6667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="mb-2 w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="italic text-sm">Belum ada data unit layanan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $serviceUnits->links() }}
    </div>

    <!-- Modal Form -->
    <div x-data="{ open: @entangle('isOpen') }" x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div class="fixed inset-0 bg-gray-900/35 backdrop-blur-[2px] transition-opacity" @click="open = false"></div>

            <div class="relative w-full max-w-xl rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-800 border border-white/20">
                <div class="mb-6 flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-xl font-black tracking-tight text-gray-900 dark:text-white uppercase">{{ $selected_id ? 'Ubah' : 'Tambah' }} Unit Layanan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Konfigurasi entitas pelayanan poliklinik atau ruangan.</p>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300 transition-colors">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-black tracking-widest uppercase text-gray-500 dark:text-gray-400">Kode Unit</label>
                            <input type="text" wire:model="code" placeholder="Cth: POLI-UMM" class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 text-sm font-mono focus:bg-white outline-none dark:bg-gray-900 dark:text-white
                                @error('code') border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-red-500 @else border-gray-200 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 @enderror">
                            @error('code')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-black tracking-widest uppercase text-gray-500 dark:text-gray-400">Tipe</label>
                            <select wire:model="type" class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 text-sm focus:bg-white outline-none dark:bg-gray-900 dark:text-white
                                @error('type') border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-red-500 @else border-gray-200 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 @enderror">
                                <option value="">Pilih Tipe</option>
                                <option value="poli">Poliklinik</option>
                                <option value="ruangan">Ruangan / Ward</option>
                                <option value="instalasi">Instalasi / OK / IGD</option>
                            </select>
                            @error('type')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-black tracking-widest uppercase text-gray-500 dark:text-gray-400">Nama Unit Layanan</label>
                        <input type="text" wire:model="name" placeholder="Nama lengkap unit..." class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 text-sm focus:bg-white outline-none dark:bg-gray-900 dark:text-white
                            @error('name') border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-red-500 @else border-gray-200 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 @enderror">
                        @error('name')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-black tracking-widest uppercase text-gray-500 dark:text-gray-400">Kategori (Opsional)</label>
                            <input type="text" wire:model="category" placeholder="Cth: poli_spesialis" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-black tracking-widest uppercase text-gray-500 dark:text-gray-400">Depo Pelayan</label>
                            <select wire:model="default_warehouse_id" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Pilih Depo</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->getTypeNameAttribute() }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-black tracking-widest uppercase text-gray-500 dark:text-gray-400">Gedung</label>
                            <input type="text" wire:model="building" placeholder="Nama Gedung" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-black tracking-widest uppercase text-gray-500 dark:text-gray-400">Lantai</label>
                            <input type="text" wire:model="floor" placeholder="Lantai" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-brand-500/20 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model="is_active" id="is_active_su" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                        <label for="is_active_su" class="text-sm font-medium text-gray-700 dark:text-gray-300">Unit Layanan Aktif</label>
                    </div>

                    <div class="mt-8 flex gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="open = false" class="w-1/3 rounded-lg border border-gray-200 bg-white py-3 text-sm font-bold text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05] transition-all uppercase tracking-widest">Batal</button>
                        <button type="submit" class="w-2/3 rounded-lg bg-indigo-600 py-3 text-sm font-bold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition-all uppercase tracking-widest">
                            {{ $selected_id ? 'Simpan Perubahan' : 'Daftarkan Unit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script for Delete Confirmation -->
    <script>
        window.addEventListener('confirm-delete', event => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Unit layanan akan dihapus permanen dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (event.detail.action === 'delete-unit') {
                        @this.delete(event.detail.id);
                    }
                }
            })
        });
    </script>
</div>
