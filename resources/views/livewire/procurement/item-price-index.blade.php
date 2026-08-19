<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">E-Catalog Harga</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola kontrak harga item dari berbagai supplier untuk pengadaan otomatis.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Harga
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input wire:model.live="search" type="text" placeholder="Cari item atau supplier..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[11px] text-left text-gray-500 dark:text-gray-400">
                <thead class="text-[10px] text-gray-400 uppercase bg-gray-50/50 dark:bg-gray-700/50 dark:text-gray-300 font-bold tracking-widest border-b dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4">Item / Obat</th>
                        <th class="px-6 py-4">Supplier Kontrak</th>
                        <th class="px-6 py-4 text-center">Tipe</th>
                        <th class="px-6 py-4 text-right">Harga Satuan</th>
                        <th class="px-6 py-4 text-center">PPN</th>
                        <th class="px-6 py-4 text-right bg-indigo-50/20">Total Netto</th>
                        <th class="px-6 py-4 text-center">Berlaku Sampai</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($prices as $price)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $price->item->name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $price->item->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">
                                {{ $price->supplier->name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded-full {{ $price->price_type === 'e-catalog' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400' }} border {{ $price->price_type === 'e-catalog' ? 'border-blue-200 dark:border-blue-500/20' : 'border-purple-200 dark:border-purple-500/20' }}">
                                    {{ $price->price_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                                <span class="text-[9px] text-gray-400 mr-1">Rp</span>{{ number_format($price->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                {{ $price->ppn_percentage }}%
                            </td>
                            <td class="px-6 py-4 text-right font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50/5">
                                <span class="text-[9px] opacity-50 mr-1">Rp</span>{{ number_format($price->price * (1 + $price->ppn_percentage/100), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center text-[10px] text-gray-500">
                                {{ $price->end_date ? date('d/m/Y', strtotime($price->end_date)) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($price->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-bold text-green-700 bg-green-100 rounded-full border border-green-200 uppercase dark:bg-green-500/15 dark:text-green-400 dark:border-green-500/20">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-bold text-red-700 bg-red-100 rounded-full border border-red-200 uppercase dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20">
                                        Mati
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="edit({{ $price->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold opacity-0 group-hover:opacity-100 transition-opacity">Edit</button>
                                <button x-on:click="$dispatch('confirm-delete', { id: {{ $price->id }}, event: 'delete-price' })" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-bold opacity-0 group-hover:opacity-100 transition-opacity">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada data harga</p>
                                    <p class="text-sm">Silahkan tambah harga baru untuk mulai mengelola.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($prices->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $prices->links() }}
            </div>
        @endif
    </div>

    @if($isOpen)
    <div class="fixed inset-0 z-[999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div class="fixed inset-0 bg-gray-500/40 backdrop-blur-[2px] transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-700">
                <form wire:submit.prevent="store">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">
                            {{ $selected_id ? 'Edit Harga Item' : 'Tambah Harga Item' }}
                        </h3>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-300 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Item / Obat</label>
                                <select wire:model="item_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                                    <option value="">-- Pilih Item --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})</option>
                                    @endforeach
                                </select>
                                @error('item_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Supplier</label>
                                <select wire:model="supplier_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tipe Harga</label>
                                <select wire:model="price_type" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                                    <option value="e-catalog">E-Catalog</option>
                                    <option value="contract">Kontrak RS</option>
                                    <option value="regular">Regular</option>
                                </select>
                                @error('price_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Harga Satuan (Sebelum PPN)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                                    <input wire:model="price" type="number" step="0.01" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                                </div>
                                @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">PPN (%)</label>
                                <input wire:model="ppn_percentage" type="number" step="0.1" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                                @error('ppn_percentage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Berlaku</label>
                                <input wire:model="effective_date" type="date" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                                @error('effective_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Berlaku Sampai</label>
                                <input wire:model="end_date" type="date" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                                @error('end_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center mt-4">
                            <input wire:model="is_active" type="checkbox" id="is_active" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded transition duration-150 ease-in-out cursor-pointer">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300 cursor-pointer">Harga Aktif</label>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                            {{ $selected_id ? 'Simpan Perubahan' : 'Simpan Harga' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
