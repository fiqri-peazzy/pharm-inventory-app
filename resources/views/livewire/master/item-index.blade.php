<div>
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between mb-6">
        <div class="relative w-full md:w-80">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M17.5 17.5L13.875 13.875M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari obat atau BMHP..."
                class="w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="openImportModal"
                class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05]">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.33333 10H16.6667M3.33333 5H16.6667M3.33333 15H16.6667" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Import Excel
            </button>
            <button wire:click="openModal"
                class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Tambah Item
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-white/[0.02] dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Item</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Satuan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</span>
                                <span class="text-xs text-gray-500">{{ $item->code }} |
                                    {{ $item->generic_name ?: '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $item->category->name }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->unit->name }}</td>
                        <td class="px-4 py-3">
                            @if ($item->is_active)
                                <span
                                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-500">Aktif</span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-400/10 dark:text-red-400">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $item->id }})"
                                    class="text-gray-400 hover:text-brand-500">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14.1667 3.33333C14.3855 3.11451 14.6453 2.94095 14.9312 2.82255C15.2171 2.70414 15.5235 2.6432 15.8333 2.6432C16.1432 2.6432 16.4496 2.70414 16.7355 2.82255C17.0214 2.94095 17.2812 3.11451 17.5 3.33333C17.7188 3.55216 17.8924 3.8119 18.0108 4.0978C18.1292 4.3837 18.1901 4.69013 18.1901 5C18.1901 5.30987 18.1292 5.6163 18.0108 5.9022C17.8924 6.1881 17.7188 6.44784 17.5 6.66667L6.66667 17.5L2.5 18.3333L3.33333 14.1667L14.1667 3.33333Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="$dispatch('confirm-delete', { id: {{ $item->id }}, action: 'delete-item' })"
                                    class="text-gray-400 hover:text-red-500">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15.8333 5.83333L15.1111 15.9444C15.0483 16.8234 14.3164 17.5 13.4355 17.5H6.56447C5.68357 17.5 4.9517 16.8234 4.88889 15.9444L4.16667 5.83333M8.33333 9.16667V14.1667M11.6667 9.16667V14.1667M13.3333 5.83333V4.16667C13.3333 3.24619 12.5871 2.5 11.6667 2.5H8.33333C7.41286 2.5 6.66667 3.24619 6.66667 4.16667V5.83333M3.33333 5.83333H16.6667"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Data tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>

    <!-- Modal Layout -->
    <div x-data="{ open: @entangle('isOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div class="fixed inset-0 bg-gray-500/40 backdrop-blur-[2px] transition-opacity" @click="open = false"></div>

            <div class="relative w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Ubah Item' : 'Tambah Item Baru' }}</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kode
                                Item</label>
                            <input type="text" wire:model="code"
                                class="w-full rounded-lg border bg-transparent px-4 py-2 text-sm outline-none dark:bg-transparent
                                @error('code') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-brand-500 dark:border-gray-800 @enderror">
                            @error('code')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Barcode</label>
                            <input type="text" wire:model="barcode"
                                class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Item</label>
                        <input type="text" wire:model="name"
                            class="w-full rounded-lg border bg-transparent px-4 py-2 text-sm outline-none dark:bg-transparent
                            @error('name') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-brand-500 dark:border-gray-800 @enderror"
                            placeholder="Contoh: Amoxicillin 500mg">
                        @error('name')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Generik</label>
                        <input type="text" wire:model="generic_name"
                            class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                            <select wire:model="item_category_id"
                                class="w-full rounded-lg border bg-transparent px-4 py-2 text-sm outline-none dark:bg-transparent
                                @error('item_category_id') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-brand-500 dark:border-gray-800 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('item_category_id')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan
                                Terkecil</label>
                            <select wire:model="item_unit_id"
                                class="w-full rounded-lg border bg-transparent px-4 py-2 text-sm outline-none dark:bg-transparent
                                @error('item_unit_id') border-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-200 focus:border-brand-500 dark:border-gray-800 @enderror">
                                <option value="">Pilih Satuan</option>
                                @foreach ($units as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                            @error('item_unit_id')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Global Stock Fields Removed - Now managed per warehouse --}}

                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="is_prescription" id="is_prescription"
                                class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800">
                            <label for="is_prescription" class="text-sm text-gray-700 dark:text-gray-300">Resep
                                Dokter</label>
                        </div>
                        {{-- <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="is_consignment" id="is_consignment" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <label for="is_consignment" class="text-sm text-gray-700 dark:text-gray-300">Titipan (Konsinyasi)</label>
                        </div> --}}
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="open = false"
                            class="w-full rounded-lg border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05]">Batal</button>
                        <button type="submit"
                            class="w-full rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-data="{ open: @entangle('isImportModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div class="fixed inset-0 bg-gray-500/40 backdrop-blur-[2px] transition-opacity" @click="open = false"></div>

            <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Import Item Obat & BMHP via Excel
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic text-[10px]">Format: <b>kode, nama, nama_generik,
                                kode_kategori, kode_satuan, ...</b></p>
                        <button wire:click="downloadTemplate"
                            class="text-xs font-medium text-brand-500 hover:text-brand-600 flex items-center gap-1 shrink-0">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 3.33333V13.3333M10 13.3333L13.3333 10M10 13.3333L6.66667 10M3.33333 16.6667H16.6667"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            Download Template
                        </button>
                    </div>

                    <div class="flex items-center justify-center w-full">
                        <label
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700
                            @error('importFile') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-700 @enderror">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                        class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                            </div>
                            <input type="file" wire:model="importFile" class="hidden" />
                        </label>
                    </div>
                    @error('importFile')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <div wire:loading wire:target="importFile" class="mt-2 text-xs text-brand-500">
                        Sedang mengunggah...
                    </div>

                    @if ($importFile)
                        <div class="mt-2 text-xs text-green-500">
                            File siap: {{ $importFile->getClientOriginalName() }}
                        </div>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="open = false"
                        class="w-full rounded-lg border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05]">Batal</button>
                    <button wire:click="import" wire:loading.attr="disabled"
                        class="w-full rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50">
                        Mulai Import
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
