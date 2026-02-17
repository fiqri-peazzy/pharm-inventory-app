<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Karantina</h1>
            <p class="text-sm text-gray-500">Quality Control (QC) & Rilis Batch oleh Apoteker</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div wire:click="$set('filterStatus', 'quarantine')"
            class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center cursor-pointer hover:bg-gray-50 transition-all">
            <div class="p-3 bg-amber-50 rounded-xl mr-4 text-amber-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pending Release</div>
                <div class="text-xl font-black text-gray-900">
                    {{ \App\Models\ItemBatch::where('status', 'quarantine')->count() }}
                </div>
            </div>
        </div>
        <div wire:click="$set('filterStatus', 'available')"
            class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center text-green-600 cursor-pointer hover:bg-gray-50 transition-all">
            <div class="p-3 bg-green-50 rounded-xl mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Released (Today)</div>
                <div class="text-xl font-black text-gray-900">
                    {{ \App\Models\ItemBatch::where('status', 'available')->whereDate('updated_at', today())->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <input wire:model.live="search" type="text" placeholder="Cari Nama Item atau No. Batch..."
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
        </div>
        <div class="w-48">
            <select wire:model.live="warehouseId"
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <select wire:model.live="filterStatus"
                class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                <option value="quarantine">Hanya Karantina</option>
                <option value="available">Telah Dirilis</option>
                <option value="expired">Ditolak / Expired</option>
            </select>
        </div>
    </div>

    @if (session()->has('success'))
        <div
            class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-bold flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Batch Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 uppercase text-[10px] font-black tracking-widest text-gray-400">
                <tr>
                    <th class="px-6 py-4 text-left">Internal Data</th>
                    <th class="px-6 py-4 text-left">Batch & Expiry</th>
                    <th class="px-6 py-4 text-center">Qty</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi QC</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($batches as $batch)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $batch->item->name }}</div>
                            <div class="text-[10px] text-gray-400 font-medium tracking-widest">{{ $batch->item->code }} |
                                {{ $batch->warehouse->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="font-mono text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded inline-block uppercase text-xs">
                                {{ $batch->batch_number }}
                            </div>
                            <div class="text-[10px] text-red-500 font-bold mt-1">ED:
                                {{ $batch->expired_date->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-base font-black text-gray-900">{{ number_format($batch->current_qty) }}</div>
                            <div class="text-[9px] text-gray-400 uppercase font-black">Unit</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $stColor = match ($batch->status) {
                                    'quarantine' => 'bg-amber-100 text-amber-700',
                                    'available' => 'bg-green-100 text-green-700',
                                    'expired' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600'
                                };
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $stColor }}">
                                {{ $batch->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($batch->status === 'quarantine')
                                <div class="flex justify-end gap-2">
                                    <button onclick="confirm('Tolak batch ini?') || event.stopImmediatePropagation()"
                                        wire:click="rejectBatch({{ $batch->id }}, 'Gagal QC')"
                                        class="px-3 py-1.5 bg-white border border-red-200 text-red-600 rounded-lg text-xs font-bold hover:bg-red-50 transition-all">
                                        Reject
                                    </button>
                                    <button wire:click="releaseBatch({{ $batch->id }})"
                                        class="px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-all flex items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="3">
                                            <path d="M20 6L9 17l-5-5"></path>
                                        </svg>
                                        Release
                                    </button>
                                </div>
                            @else
                                <span class="text-[10px] text-gray-300 italic font-bold">Action Completed</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($batches->isEmpty())
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-400 italic">
                            Tidak ada data ditemukan.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50">
            {{ $batches->links() }}
        </div>
    </div>
</div>