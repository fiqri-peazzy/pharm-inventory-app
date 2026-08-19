<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.distributions.index') }}" class="w-10 h-10 bg-white dark:bg-white/[0.03] border border-gray-100 dark:border-gray-800 rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-brand-500 hover:border-brand-500 transition-all shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Proses Pengiriman Stok</h2>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mt-1 block">Fulfillment & Batch Allocation (FEFO)</span>
            </div>
        </div>

        <div class="text-right">
            <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest block mb-1">No. Transaksi</span>
            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400 italic bg-indigo-50 dark:bg-indigo-500/15 px-3 py-1 rounded-lg border border-indigo-100 dark:border-indigo-500/20">{{ $distribution->distribution_number }}</span>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 dark:bg-red-500/15 border border-red-100 dark:border-red-500/20 text-red-600 dark:text-red-400 rounded-xl text-xs font-bold uppercase italic">
            {{ session('error') }}
        </div>
    @endif

    <!-- Transaction Header -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm col-span-2">
            <div class="flex items-center justify-between mb-6">
                <div class="flex-1">
                    <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] block mb-2">Gudang Pengirim (Asal)</span>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-white/[0.03] rounded-xl flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-100 dark:border-gray-800">
                             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        </div>
                        <span class="text-lg font-black text-gray-900 dark:text-white italic">{{ $distribution->origin->name }}</span>
                    </div>
                </div>
                <div class="px-8 text-gray-200">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                </div>
                <div class="flex-1 text-right">
                    <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] block mb-2">Unit Penerima (Tujuan)</span>
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-lg font-black text-brand-600 italic">{{ $distribution->destination->name }}</span>
                        <div class="w-10 h-10 bg-brand-50 rounded-xl flex items-center justify-center text-brand-500 border border-brand-100">
                             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-gray-50 dark:bg-white/[0.03] rounded-xl border border-gray-100 dark:border-gray-800">
                <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest block mb-2 italic">Catatan Permintaan:</span>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $distribution->notes ?? 'Tidak ada catatan.' }}</p>
            </div>
        </div>

        <div class="bg-gray-900 rounded-3xl p-6 relative overflow-hidden shadow-xl shadow-gray-200 group flex flex-col justify-center">
            <div class="relative z-10">
                <h4 class="text-white/40 text-[9px] font-black uppercase tracking-[0.2em] mb-3">Health Status</h4>
                <div class="flex items-baseline gap-2 mb-4">
                    <span class="text-white text-3xl font-black italic tracking-tighter">FEFO</span>
                    <span class="text-emerald-400 text-[10px] font-bold uppercase tracking-widest italic animate-pulse">ENFORCED</span>
                </div>
                <p class="text-[9px] text-white/50 leading-relaxed font-medium uppercase tracking-[0.1em]">Sistem otomatis menyarankan batch dengan masa expired terdekat untuk diproses terlebih dahulu.</p>
            </div>
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-brand-500/20 rounded-full blur-3xl group-hover:bg-brand-500/30 transition-all duration-1000"></div>
        </div>
    </div>

    <!-- Fulfillment Table -->
    <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
            <h3 class="text-[11px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300">Daftar Barang & Alokasi Batch</h3>
            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-[9px] font-black text-gray-400 dark:text-gray-500 rounded-lg uppercase italic">{{ count($items) }} Items to Ship</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-4 font-black text-gray-400 dark:text-gray-500 uppercase text-center w-24">Permintaan</th>
                    @forelse($items as $index => $item)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.03] transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-black text-gray-900 dark:text-white block">{{ $item['name'] }}</span>
                                <span class="text-[9px] text-gray-400 dark:text-gray-500 font-bold uppercase italic tracking-widest">{{ $item['code'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-gray-600 dark:text-gray-300 text-lg">{{ number_format($item['qty_requested']) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <select wire:model.live="items.{{ $index }}.item_batch_id" class="w-full bg-gray-50 dark:bg-white/[0.03] border rounded-lg text-xs px-3 py-2.5 font-bold text-gray-700 dark:text-gray-300 focus:ring-2 transition-all
                                    @error('items.'.$index.'.item_batch_id') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @else border-gray-100 focus:ring-brand-500 focus:border-brand-500 dark:border-gray-800 @enderror">
                                    <option value="">Pilih Batch</option>
                                    @foreach($item['available_batches'] as $batch)
                                        <option value="{{ $batch['id'] }}">
                                            {{ $batch['batch_number'] }} (ED: {{ \Carbon\Carbon::parse($batch['expired_date'])->format('d/m/y') }}) | Stok: {{ number_format($batch['current_qty']) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('items.'.$index.'.item_batch_id')
                                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </td>
                             <td class="px-6 py-4 text-center">
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-lg italic">{{ number_format($item['destination_stock']) }}</span>
                                <span class="text-[8px] font-black text-gray-400 dark:text-gray-500 uppercase block tracking-tighter">Stok Tujuan</span>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" wire:model="items.{{ $index }}.qty_sent" step="1" class="w-full bg-indigo-50 dark:bg-indigo-500/15 border rounded-lg text-sm px-3 py-2 text-center font-black text-indigo-600 dark:text-indigo-400 focus:ring-2 transition-all shadow-inner
                                    @error('items.'.$index.'.qty_sent') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @else border-indigo-100 focus:ring-indigo-500 focus:border-indigo-500 dark:border-indigo-500/20 @enderror">
                                @error('items.'.$index.'.qty_sent')
                                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </td>
                        </tr>
                        <tr class="bg-gray-50/20">
                            <td colspan="5" class="px-6 py-2 border-b border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-2">
                                    <span class="text-[8px] font-black text-gray-400 dark:text-gray-500 uppercase italic">Catatan Item:</span>
                                    <input type="text" wire:model="items.{{ $index }}.notes" placeholder="Misal: Stok sisa sedikit, baru dikirim sebagian..." class="flex-1 bg-transparent border-none text-[10px] font-medium text-gray-500 dark:text-gray-400 placeholder:text-gray-300 focus:ring-0 py-0">
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center italic text-gray-300 font-medium">Data permintaan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!empty($items))
            <div class="p-6 bg-gray-50/30 dark:bg-white/[0.02] flex justify-end gap-3">
                <button wire:click="save" class="px-8 py-3 bg-brand-500 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-brand-600 transition-all shadow-lg hover:shadow-brand-200 flex items-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Proses Kirim (Post)
                </button>
            </div>
        @endif
    </div>
</div>
