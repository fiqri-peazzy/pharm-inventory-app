<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Permintaan Barang (PR)</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola pengajuan kebutuhan barang dari unit/depo ke instalasi farmasi.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat PR Baru
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
                <input wire:model.live="search" type="text" placeholder="Cari nomor PR..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-4 font-semibold">No. Request</th>
                        <th class="px-6 py-4 font-semibold">Tgl Pengajuan</th>
                        <th class="px-6 py-4 font-semibold text-center">Periode</th>
                        <th class="px-6 py-4 font-semibold">Gudang Peminta</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $request->request_number }}
                            </td>
                            <td class="px-6 py-4">
                                {{ date('d/m/Y', strtotime($request->request_date)) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{ date('F', mktime(0, 0, 0, $request->period_month, 1)) }} {{ $request->period_year }}
                            </td>
                            <td class="px-6 py-4 font-medium">
                                {{ $request->warehouse->name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Detail</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada permintaan barang</p>
                                    <p class="text-sm">Silahkan tambah PR baru untuk mulai pengajuan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Large) -->
    @if($isOpen)
    <div class="fixed inset-0 z-[999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-200 dark:border-gray-700">
                <form wire:submit.prevent="store">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-indigo-50 dark:bg-gray-700/50 text-indigo-900 dark:text-white">
                        <div>
                            <h3 class="text-lg font-bold" id="modal-title">Buat Permintaan Barang Baru</h3>
                            <p class="text-xs opacity-70">Lengkapi data untuk mengajukan kebutuhan barang.</p>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        <!-- Header Section -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">No. Request</label>
                                <input type="text" wire:model="request_number" readonly class="block w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono text-indigo-600 font-bold focus:ring-0">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Unit/Gudang Peminta</label>
                                <select wire:model="warehouse_id" class="block w-full px-3 py-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Tanggal Request</label>
                                <input type="date" wire:model="request_date" class="block w-full px-3 py-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Untuk Periode</label>
                                <div class="flex gap-2">
                                    <select wire:model="period_month" class="block w-7/12 px-2 py-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs focus:ring-indigo-500">
                                        @foreach(range(1,12) as $m)
                                            <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" wire:model="period_year" class="block w-5/12 px-2 py-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-xs text-center font-bold focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b pb-2 dark:border-gray-700">
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">Daftar Barang yang Diminta</h4>
                                <button type="button" wire:click="addItem" class="text-xs py-1 px-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-md border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors font-semibold">
                                    + Tambah Item
                                </button>
                            </div>

                            <div class="border dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                                <table class="w-full text-[11px] text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-400 font-bold uppercase tracking-widest border-b dark:border-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 w-10 text-center">#</th>
                                            <th class="px-4 py-3 min-w-[300px]">Item / Nama Obat</th>
                                            <th class="px-4 py-3 w-32 border-l dark:border-gray-700 text-center">Qty Diminta</th>
                                            <th class="px-4 py-3">Catatan Khusus</th>
                                            <th class="px-4 py-3 w-12 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach($items as $index => $item)
                                            <tr class="align-top group hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-4 text-center text-gray-300 font-bold">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 border-l dark:border-gray-700">
                                                    <select wire:model="items.{{ $index }}.item_id" class="w-full border-gray-200 dark:border-gray-600 rounded-lg p-2 focus:ring-1 focus:ring-indigo-500 dark:bg-gray-800 text-xs">
                                                        <option value="">-- Pilih Obat/Item --</option>
                                                        @foreach($available_items as $ai)
                                                            <option value="{{ $ai->id }}">{{ $ai->name }} ({{ $ai->code }})</option>
                                                        @endforeach
                                                    </select>
                                                    @error('items.'.$index.'.item_id') <span class="text-red-500 text-[9px] font-bold block mt-1">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-4 py-3 border-l dark:border-gray-700 bg-indigo-50/10 dark:bg-indigo-900/5">
                                                    <input type="number" wire:model="items.{{ $index }}.requested_qty" class="w-full border-gray-200 dark:border-gray-600 rounded-lg p-2 text-center font-black text-indigo-700 dark:text-indigo-400 bg-white dark:bg-gray-800 focus:ring-1 focus:ring-indigo-500 shadow-inner">
                                                    @error('items.'.$index.'.requested_qty') <span class="text-red-500 text-[9px] font-bold block mt-1 text-center">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-4 py-3 border-l dark:border-gray-700">
                                                    <input type="text" wire:model="items.{{ $index }}.notes" placeholder="Tambahkan alasan..." class="w-full bg-transparent border-none p-2 placeholder-gray-400 text-xs focus:ring-0 focus:border-b-indigo-500 focus:border-b transition-all italic">
                                                </td>
                                                <td class="px-4 py-3 text-center vertical-middle">
                                                    <button type="button" wire:click="removeItem({{ $index }})" class="text-red-300 hover:text-red-600 transition-colors opacity-0 group-hover:opacity-100">
                                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Catatan Tambahan (Header)</label>
                            <textarea wire:model="notes" rows="2" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700" placeholder="Opsional..."></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-3 rounded-b-xl">
                        <button type="button" wire:click="closeModal" class="px-5 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-md transform active:scale-95 transition-all">
                            Kirim Permintaan Ke Direktur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
