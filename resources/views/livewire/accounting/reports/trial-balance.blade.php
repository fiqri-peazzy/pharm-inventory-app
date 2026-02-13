<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <label class="block mb-1 text-xs font-bold text-gray-700 ml-1 italic uppercase tracking-wider">Per Tanggal</label>
            <input type="date" wire:model.live="dateTo" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
        </div>

        <div class="flex items-center gap-3">
            @if($isBalanced)
                <div class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 border border-green-100 rounded-xl text-xs font-black uppercase tracking-widest">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    BALANCED
                </div>
            @else
                <div class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 border border-red-100 rounded-xl text-xs font-black uppercase tracking-widest">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    NOT BALANCED
                </div>
            @endif

            <button wire:click="$refresh" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all flex items-center gap-2 h-[42px]">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-black text-gray-900 uppercase">NERACA SALDO (TRIAL BALANCE)</h2>
            <p class="text-sm text-gray-500 font-medium whitespace-nowrap overflow-hidden text-ellipsis">Posisi Keuangan per <span class="text-brand-600 font-bold italic">{{ \Carbon\Carbon::parse($dateTo)->format('d F Y') }}</span></p>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="bg-white border-b border-gray-100">
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500">Kode Akun</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500">Nama Akun</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 text-right">Debit</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 text-right">Kredit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($rows as $row)
                    <tr class="group hover:bg-gray-50/50 transition-all">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-brand-600 tracking-tighter">{{ $row['code'] }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $row['name'] }}</td>
                        <td class="px-6 py-4 text-right font-black text-gray-900">
                            {{ $row['debit'] > 0 ? number_format($row['debit']) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-black text-amber-600">
                            {{ $row['credit'] > 0 ? number_format($row['credit']) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                            Tidak ada saldo akun per tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-100">
                <tr class="font-black text-gray-900">
                    <td colspan="2" class="px-6 py-6 text-right uppercase tracking-[0.2em] text-xs font-black text-gray-500">TOTAL KESELURUHAN</td>
                    <td class="px-6 py-6 text-right text-lg border-l border-white shadow-[inset_1px_0_0_0_rgba(0,0,0,0.05)]">Rp {{ number_format($totalDebit) }}</td>
                    <td class="px-6 py-6 text-right text-lg text-amber-600 border-l border-white shadow-[inset_1px_0_0_0_rgba(0,0,0,0.05)]">Rp {{ number_format($totalCredit) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if(!$isBalanced)
        <div class="p-4 rounded-xl bg-red-50 border border-red-100 flex items-start gap-4 animate-pulse">
            <div class="p-2 bg-red-100 rounded-lg text-red-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-red-900 uppercase">Peringatan: Jurnal Tidak Seimbang!</h4>
                <p class="text-xs text-red-700 mt-1">Terdapat perbedaan antara total debit dan total kredit sebesar <span class="font-black">Rp {{ number_format(abs($totalDebit - $totalCredit)) }}</span>. Mohon periksa kembali posting jurnal Anda.</p>
            </div>
        </div>
    @endif
</div>
