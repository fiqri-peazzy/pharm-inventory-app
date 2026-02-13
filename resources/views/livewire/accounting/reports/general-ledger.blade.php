<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-end gap-4">
        <div class="flex-1 min-w-[300px]">
            <label class="block mb-1 text-xs font-bold text-gray-700 ml-1 italic uppercase tracking-wider">Pilih Akun</label>
            <select wire:model.live="accountId" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                <option value="">-- Silahkan Pilih Akun --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 text-xs font-bold text-gray-700 ml-1 italic uppercase tracking-wider">Dari Tanggal</label>
            <input type="date" wire:model.live="dateFrom" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
        </div>

        <div>
            <label class="block mb-1 text-xs font-bold text-gray-700 ml-1 italic uppercase tracking-wider">Sampai Tanggal</label>
            <input type="date" wire:model.live="dateTo" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
        </div>

        <button wire:click="$refresh" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all flex items-center gap-2 h-[42px]">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            Refresh
        </button>
    </div>

    @if(!$accountId)
        <div class="bg-white rounded-2xl p-12 border border-gray-100 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-300"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Belum Ada Akun Terpilih</h3>
            <p class="text-sm text-gray-400 max-w-sm mt-1">Silahkan pilih akun dari drop-down di atas untuk melihat rincian buku besar.</p>
        </div>
    @else
        <!-- Report Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-gray-900 uppercase">BUKU BESAR (GENERAL LEDGER)</h2>
                    <p class="text-sm text-gray-500 font-medium">Akun: <span class="text-brand-600 font-bold">{{ $account->code }} - {{ $account->name }}</span> | Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1">Normal Balance</span>
                    <span class="px-3 py-1 bg-{{ $account->normal_balance == 'debit' ? 'blue' : 'amber' }}-100 text-{{ $account->normal_balance == 'debit' ? 'blue' : 'amber' }}-700 text-xs font-black uppercase rounded-lg border border-{{ $account->normal_balance == 'debit' ? 'blue' : 'amber' }}-200">
                        {{ $account->normal_balance }}
                    </span>
                </div>
            </div>

            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500">Tanggal / Jam</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500">No. Jurnal</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500">Keterangan</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 text-right">Debit</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 text-right text-amber-600/70">Kredit</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-500 text-right border-l border-gray-50 bg-gray-50/30">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    <!-- Opening Balance -->
                    <tr class="bg-brand-50/30 italic">
                        <td class="px-6 py-4 text-gray-400">---</td>
                        <td class="px-6 py-4 text-gray-400 font-mono tracking-tighter">OPENING</td>
                        <td class="px-6 py-4 font-bold text-brand-700">Saldo Awal per {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">0</td>
                        <td class="px-6 py-4 text-right">0</td>
                        <td class="px-6 py-4 text-right font-black text-gray-900 border-l border-gray-100 bg-gray-50/50">
                            {{ number_format($openingBalance) }}
                        </td>
                    </tr>

                    @php $currentBalance = $openingBalance; @endphp
                    @forelse($items as $item)
                        @php 
                            if ($account->normal_balance === 'debit') {
                                $currentBalance += ($item->debit - $item->credit);
                            } else {
                                $currentBalance += ($item->credit - $item->debit);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="px-6 py-4">
                                <span class="block font-medium text-gray-700">{{ $item->journalEntry->journal_date->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-gray-400">{{ $item->journalEntry->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('accounting.journals.show', $item->journal_entry_id) }}" class="text-brand-600 font-bold hover:underline">{{ $item->journalEntry->journal_number }}</a>
                            </td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $item->description ?: $item->journalEntry->description }}">
                                {{ $item->description ?: $item->journalEntry->description }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium {{ $item->debit > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                                {{ number_format($item->debit) }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium {{ $item->credit > 0 ? 'text-amber-600' : 'text-gray-300' }}">
                                {{ number_format($item->credit) }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-gray-900 border-l border-gray-50 bg-gray-50/20">
                                {{ number_format($currentBalance) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                Tidak ada transaksi dalam periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50/80 font-black">
                        <td colspan="3" class="px-6 py-4 text-right uppercase tracking-widest text-xs">Total Mutasi & Saldo Akhir</td>
                        <td class="px-6 py-4 text-right text-gray-900">{{ number_format($items->sum('debit')) }}</td>
                        <td class="px-6 py-4 text-right text-amber-600">{{ number_format($items->sum('credit')) }}</td>
                        <td class="px-6 py-4 text-right text-brand-600 border-l border-gray-200 bg-brand-50/50">{{ number_format($currentBalance) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
