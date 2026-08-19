<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.distributions.index') }}" class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-brand-500 hover:border-brand-500 transition-all shadow-sm dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-500">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div>
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight dark:text-white">Konfirmasi Penerimaan Stok</h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 block dark:text-gray-500">Verification & Stock Receipt (Check Physical)</span>
            </div>
        </div>

        <div class="text-right">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1 dark:text-gray-500">No. Transaksi</span>
            <span class="text-sm font-black text-emerald-600 italic bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20">{{ $distribution->distribution_number }}</span>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-xs font-bold uppercase italic dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20">
            {{ session('error') }}
        </div>
    @endif

    <!-- Transaction Header -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2 dark:text-gray-500">Dikirim Dari</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-500">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                    </div>
                    <span class="text-lg font-black text-gray-900 italic dark:text-white">{{ $distribution->origin->name }}</span>
                </div>
            </div>
            <div class="px-8 text-gray-200 dark:text-gray-700">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </div>
            <div class="flex-1 text-right">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-2 dark:text-gray-500">Diterima Di (Unit Anda)</span>
                <div class="flex items-center justify-end gap-3">
                    <span class="text-lg font-black text-emerald-600 italic dark:text-emerald-400">{{ $distribution->destination->name }}</span>
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-100 dark:bg-emerald-500/15 dark:border-emerald-500/20 dark:text-emerald-400">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-10 mt-6 pt-6 border-t border-gray-50 dark:border-gray-800">
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1 dark:text-gray-500">Dikirim Oleh</span>
                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $distribution->sender->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1 dark:text-gray-500">Tanggal Kirim</span>
                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $distribution->sent_at?->format('d M Y H:i') ?? '-' }}</span>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1 italic dark:text-gray-500">Catatan:</span>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $distribution->notes ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Receiving Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-emerald-50/10 dark:border-gray-800 dark:bg-emerald-500/5">
            <h3 class="text-[11px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Verifikasi Fisik Barang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 dark:bg-white/[0.02] dark:border-gray-800">
                        <th class="px-6 py-4 font-black text-gray-400 uppercase dark:text-gray-500">Barang / Batch</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-32 font-bold dark:text-gray-500">Qty Dikirim</th>
                        <th class="px-6 py-4 font-black text-gray-400 uppercase text-center w-40 font-black dark:text-gray-500">Qty Diterima (Real)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($items as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors dark:hover:bg-white/[0.03]">
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 block dark:text-white">{{ $item['name'] }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase italic tracking-widest border-r border-gray-200 pr-2 dark:text-gray-500 dark:border-gray-800">Batch: {{ $item['batch_number'] }}</span>
                                    <span class="text-[9px] font-bold text-amber-500 uppercase italic dark:text-amber-400">ED: {{ $item['expired_date'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-gray-400 text-lg italic dark:text-gray-500">{{ number_format($item['qty_sent']) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" wire:model="items.{{ $index }}.qty_received" step="1" class="w-full bg-emerald-50 border rounded-lg text-sm px-3 py-2 text-center font-black text-emerald-600 focus:ring-2 transition-all shadow-inner dark:bg-emerald-500/15 dark:text-emerald-400
                                    @error('items.'.$index.'.qty_received') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @else border-emerald-100 focus:ring-emerald-500 focus:border-emerald-500 dark:border-emerald-500/20 @enderror">
                                @error('items.'.$index.'.qty_received')
                                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-6 bg-gray-50/30 flex justify-end dark:bg-white/[0.02]">
            <button wire:click="save" class="px-8 py-3 bg-emerald-500 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all shadow-lg hover:shadow-emerald-200 flex items-center gap-2 dark:hover:shadow-emerald-900/30">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Konfirmasi Terima & Update Stok
            </button>
        </div>
    </div>
</div>
