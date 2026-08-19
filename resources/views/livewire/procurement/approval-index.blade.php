<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Approval Direktur</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Tinjau dan setujui permintaan barang dari berbagai unit pelayanan.</p>
        </div>
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
                        <th class="px-6 py-4 font-semibold">Peminta</th>
                        <th class="px-6 py-4 font-semibold">Unit/Gudang</th>
                        <th class="px-6 py-4 font-semibold text-center">Catatan</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $request->request_number }}
                            </td>
                            <td class="px-6 py-4">
                                {{ date('d/m/Y', strtotime($request->request_date)) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $request->creator->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $request->warehouse->name }}
                            </td>
                            <td class="px-6 py-4 truncate max-w-[200px]">
                                {{ $request->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="show({{ $request->id }})" class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 text-xs font-bold rounded shadow-sm transition-all uppercase tracking-wider">
                                    Tinjau PR
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada PR yang menunggu persetujuan</p>
                                    <p class="text-sm">Semua ajuan telah diproses.</p>
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

    <!-- Review Modal -->
    @if($isOpen && $selected_request)
    <div class="fixed inset-0 z-[999] overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div class="fixed inset-0 bg-gray-500/40 backdrop-blur-[2px] transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tinjauan Permintaan Barang</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $selected_request->request_number }} | Diajukan oleh: {{ $selected_request->creator->name }}</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg">
                            <h4 class="text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase mb-2">Informasi Unit</h4>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $selected_request->warehouse->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">Periode: {{ date('F', mktime(0, 0, 0, $selected_request->period_month, 1)) }} {{ $selected_request->period_year }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg">
                            <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Catatan Peminta</h4>
                            <p class="text-sm italic text-gray-700 dark:text-gray-300">"{{ $selected_request->notes ?? 'Tidak ada catatan' }}"</p>
                        </div>
                    </div>

                    <div class="mb-4 flex items-center justify-between">
                         <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">Detail Barang & Koreksi Jumlah</h4>
                         <span class="text-[10px] bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 px-2 py-1 rounded font-bold uppercase tracking-widest border border-red-100 dark:border-red-800">Cek kuantitas sebelum setuju</span>
                    </div>

                    <div class="border dark:border-gray-700 rounded-xl overflow-hidden shadow-sm bg-white dark:bg-gray-800">
                        <table class="w-full text-[11px] text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-400 font-bold uppercase tracking-widest border-b dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-3 w-2/5">Nama Item / Obat</th>
                                    <th class="px-4 py-3 w-24 text-center">Qty Minta</th>
                                    <th class="px-4 py-3 w-32 text-center text-indigo-600 dark:text-indigo-400 border-x dark:border-gray-700 bg-indigo-50/30">Qty Disetujui</th>
                                    <th class="px-4 py-3">Catatan Item</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($details as $index => $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-4 font-bold text-gray-800 dark:text-gray-200">
                                        {{ $item['item_name'] }}
                                    </td>
                                    <td class="px-4 py-4 text-center text-gray-500 dark:text-gray-400 font-medium">
                                        {{ number_format($item['requested_qty']) }}
                                    </td>
                                    <td class="px-4 py-3 border-x dark:border-gray-700 bg-indigo-50/20 dark:bg-indigo-900/10">
                                        <input type="number" wire:model="details.{{ $index }}.approved_qty" class="w-full px-3 py-2 border-gray-200 dark:border-gray-600 rounded-lg text-center font-black text-indigo-700 dark:text-indigo-400 bg-white dark:bg-gray-800 focus:ring-1 focus:ring-indigo-500 shadow-sm">
                                    </td>
                                    <td class="px-4 py-4 text-gray-400 dark:text-gray-500 italic text-[10px]">
                                        {{ $selected_request->details[$index]->notes ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center rounded-b-xl">
                    <button type="button" wire:click="reject" class="px-6 py-2 text-[10px] font-black text-red-600 dark:text-red-400 bg-white dark:bg-gray-800 border-2 border-red-600 dark:border-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all uppercase tracking-widest shadow-sm active:scale-95">
                        Tolak Permintaan
                    </button>
                    <div class="flex space-x-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-[10px] font-black text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 uppercase tracking-widest transition-all">
                            Tutup
                        </button>
                        <button type="button" wire:click="approve" class="px-8 py-2 text-[10px] font-black text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 shadow-lg shadow-green-500/20 active:scale-95 transition-all uppercase tracking-widest">
                            Setujui
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
