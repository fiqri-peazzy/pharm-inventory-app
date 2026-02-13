<div>
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between mb-6">
        <div class="relative w-full md:w-80">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.5 17.5L13.875 13.875M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari supplier..." class="w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
        </div>
        
        <button wire:click="openModal" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tambah Supplier
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-white/[0.02] dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Kontak</th>
                    <th class="px-4 py-3">Termin</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($suppliers as $supplier)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</span>
                                <span class="text-xs text-gray-500">{{ $supplier->code }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">{{ strtoupper($supplier->type) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-700 dark:text-gray-300">{{ $supplier->contact_person }}</span>
                                <span class="text-xs text-gray-500">{{ $supplier->phone }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $supplier->payment_term }} Hari</td>
                        <td class="px-4 py-3">
                            @if ($supplier->is_active)
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-500">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-400/10 dark:text-red-400">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $supplier->id }})" class="text-gray-400 hover:text-brand-500">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.1667 3.33333C14.3855 3.11451 14.6453 2.94095 14.9312 2.82255C15.2171 2.70414 15.5235 2.6432 15.8333 2.6432C16.1432 2.6432 16.4496 2.70414 16.7355 2.82255C17.0214 2.94095 17.2812 3.11451 17.5 3.33333C17.7188 3.55216 17.8924 3.8119 18.0108 4.0978C18.1292 4.3837 18.1901 4.69013 18.1901 5C18.1901 5.30987 18.1292 5.6163 18.0108 5.9022C17.8924 6.1881 17.7188 6.44784 17.5 6.66667L6.66667 17.5L2.5 18.3333L3.33333 14.1667L14.1667 3.33333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button wire:click="$dispatch('confirm-delete', { id: {{ $supplier->id }}, action: 'delete-supplier' })" class="text-gray-400 hover:text-red-500">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8333 5.83333L15.1111 15.9444C15.0483 16.8234 14.3164 17.5 13.4355 17.5H6.56447C5.68357 17.5 4.9517 16.8234 4.88889 15.9444L4.16667 5.83333M8.33333 9.16667V14.1667M11.6667 9.16667V14.1667M13.3333 5.83333V4.16667C13.3333 3.24619 12.5871 2.5 11.6667 2.5H8.33333C7.41286 2.5 6.66667 3.24619 6.66667 4.16667V5.83333M3.33333 5.83333H16.6667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
        {{ $suppliers->links() }}
    </div>

    <!-- Modal Layout -->
    <div x-data="{ open: @entangle('isOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

            <div class="relative w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $isEdit ? 'Ubah Supplier' : 'Tambah Supplier Baru' }}</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Supplier</label>
                            <input type="text" wire:model="code" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                            @error('code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label>
                            <select wire:model="type" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                                <option value="">Pilih Tipe</option>
                                <option value="pbf">PBF (Farmasi)</option>
                                <option value="distributor">Distributor</option>
                                <option value="manufaktur">Manufaktur</option>
                                <option value="toko">Toko</option>
                            </select>
                            @error('type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Supplier</label>
                        <input type="text" wire:model="name" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kontak Person</label>
                            <input type="text" wire:model="contact_person" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Telepon/WA</label>
                            <input type="text" wire:model="phone" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                            @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email (Untuk Notifikasi)</label>
                            <input type="email" wire:model="email" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                            @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                        <textarea wire:model="address" rows="2" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">NPWP</label>
                            <input type="text" wire:model="npwp" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Pajak</label>
                            <select wire:model="tax_status" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                                <option value="non_pkp">Non PKP</option>
                                <option value="pkp">PKP</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Termin (Hari)</label>
                            <input type="number" wire:model="payment_term" class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-transparent">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="is_active" id="is_active_supplier" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <label for="is_active_supplier" class="text-sm text-gray-700 dark:text-gray-300">Supplier Aktif</label>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="open = false" class="w-full rounded-lg border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="w-full rounded-lg bg-brand-500 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Supplier' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
