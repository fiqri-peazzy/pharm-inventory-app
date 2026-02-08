<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.distributions.index') }}" class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-brand-500 hover:border-brand-500 transition-all shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div>
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Konfirmasi Penerimaan Stok</h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 block">Verification & Stock Receipt (Check Physical)</span>
            </div>
        </div>

        <div class="text-right">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">No. Transaksi</span>
            <span class="text-sm font-black text-emerald-600 italic bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100">{{ $distribution->distribution_number }}</span>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-xs font-bold uppercase italic">
            {{ session('error') }}
        </div>
    @endif

    <!-- Transaction Header -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2">Dikirim Dari</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                    </div>
                    <span class="text-lg font-black text-gray-900 italic">{{ $distribution->origin->name }}</span>
                </div>
            </div>
            <div class="px-8 text-gray-200">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </div>
            <div class="flex-1 text-right">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2">Diterima Di (Unit Anda)</span>
                <div class="flex items-center justify-end gap-3">
                    <span class="text-lg font-black text-emerald-600 italic">{{ $distribution->destination->name }}</span>
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-100">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-10 mt-6 pt-6 border-t border-gray-50">
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Dikirim Oleh</span>
                <span class="text-xs font-bold text-gray-700">{{ $distribution->sender->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Tanggal Kirim</span>
                <span class="text-xs font-bold text-gray-700">{{ $distribution->sent_at?->format('d M Y H:i') ?? '-' }}</span>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1 italic">Catatan:</span>
                <p class="text-xs font-medium text-gray-500">{{ $distribution->notes ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Receiving Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-emerald-50/10">
            <h3 class="text-[11px] font-black uppercase tracking-widest text-emerald-600">Verifikasi Fisik Barang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 font-black text-gray-400 uppercase">Barang / Batch</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-32 font-bold">Qty Dikirim</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-40 font-black">Qty Diterima (Real)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($items as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 block">{{ $item['name'] }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase italic tracking-widest border-r border-gray-200 pr-2">Batch: {{ $item['batch_number'] }}</span>
                                    <span class="text-[9px] font-bold text-amber-500 uppercase italic">ED: {{ $item['expired_date'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-gray-400 text-lg italic">{{ number_format($item['qty_sent']) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" wire:model="items.{{ $index }}.qty_received" step="1" class="w-full bg-emerald-50 border-emerald-100 rounded-lg text-sm px-3 py-2 text-center font-black text-emerald-600 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-inner">
                                @error('items.'.$index.'.qty_received') <span class="text-[8px] text-red-500 font-bold uppercase block mt-1">Invalid</span> @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-6 bg-gray-50/30 flex justify-end">
            <button wire:click="save" class="px-8 py-3 bg-emerald-500 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all shadow-lg hover:shadow-emerald-200 flex items-center gap-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Konfirmasi Terima & Update Stok
            </button>
        </div>
    </div>
</div>
