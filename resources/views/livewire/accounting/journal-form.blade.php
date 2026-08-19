<div class="space-y-6">
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex items-center gap-4">
            <a href="{{ route('accounting.journals.index') }}" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all dark:text-gray-500 dark:hover:bg-brand-500/10">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 dark:text-white">{{ $isEdit ? ($isViewOnly ? 'Detail Jurnal' : 'Edit Jurnal') : 'Input Jurnal Manual' }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $isEdit ? $journal_number : 'Buat entri jurnal akuntansi baru' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if(!$isViewOnly)
                <button wire:click="save('draft')" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-200 transition-all dark:bg-white/[0.05] dark:text-gray-300 dark:hover:bg-white/[0.1]">
                    Simpan Draft
                </button>
                <button wire:click="save('posted')" class="px-4 py-2 bg-brand-500 text-white text-sm font-bold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Posting Sekarang
                </button>
            @elseif($isEdit && $status === 'draft')
                <button wire:click="post" class="px-4 py-2 bg-brand-500 text-white text-sm font-bold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all flex items-center gap-2">
                    Posting Jurnal
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Header Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4 dark:bg-white/[0.03] dark:border-gray-800">
                <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2 dark:text-gray-500">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Informasi Jurnal
                </h2>

                <div class="space-y-3">
                    <label class="block">
                        <span class="text-xs font-bold text-gray-700 ml-1 dark:text-gray-300">Tanggal Jurnal</span>
                        <input type="date" wire:model="journal_date" {{ $isViewOnly ? 'disabled' : '' }} class="mt-1 block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all disabled:opacity-60 dark:bg-white/[0.03] dark:border-gray-800 dark:text-white dark:focus:bg-white/[0.05]">
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold text-gray-700 ml-1 dark:text-gray-300">Tipe Jurnal</span>
                        <select wire:model="type" {{ $isViewOnly ? 'disabled' : '' }} class="mt-1 block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white dark:focus:bg-white/[0.05]">
                            <option value="standard">Standard</option>
                            <option value="adjusting">Adjusting</option>
                            <option value="closing">Closing</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold text-gray-700 ml-1 dark:text-gray-300">No. Referensi (Opsional)</span>
                        <input type="text" wire:model="reference" {{ $isViewOnly ? 'disabled' : '' }} placeholder="Contoh: INV/2024/001" class="mt-1 block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all disabled:opacity-60 dark:bg-white/[0.03] dark:border-gray-800 dark:text-white dark:focus:bg-white/[0.05]">
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold text-gray-700 ml-1 dark:text-gray-300">Keterangan Jurnal</span>
                        <textarea wire:model="description" rows="3" {{ $isViewOnly ? 'disabled' : '' }} placeholder="Berikan penjelasan singkat mengenai entri jurnal ini..." class="mt-1 block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all disabled:opacity-60 dark:bg-white/[0.03] dark:border-gray-800 dark:text-white dark:focus:bg-white/[0.05]"></textarea>
                    </label>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-gray-900 p-6 rounded-2xl shadow-xl shadow-gray-200 text-white space-y-4">
                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Total Debit</span>
                    <span class="text-lg font-black tracking-tight text-brand-400">Rp{{ number_format($total_debit) }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-white/10 pb-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Total Kredit</span>
                    <span class="text-lg font-black tracking-tight text-blue-400">Rp{{ number_format($total_credit) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Selisih (Balance)</span>
                    <span class="text-xl font-black {{ $difference == 0 ? 'text-green-400' : 'text-red-400' }}">
                        Rp{{ number_format($difference) }}
                    </span>
                </div>
                
                @if($difference != 0)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-red-400/10 border border-red-400/20">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-red-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <p class="text-[10px] font-black uppercase text-red-100">Jurnal Belum Balance!</p>
                    </div>
                @else
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-green-400/10 border border-green-400/20 text-green-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <p class="text-[10px] font-black uppercase">Jurnal Sudah Seimbang</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Entries Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
                <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between dark:bg-white/[0.02] dark:border-gray-800">
                    <h3 class="text-sm font-black text-gray-900 flex items-center gap-2 uppercase tracking-wide dark:text-white">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"></path></svg>
                        Rincian Baris Jurnal
                    </h3>
                    @if(!$isViewOnly)
                        <button wire:click="addEntry()" class="px-3 py-1.5 bg-white border border-gray-200 text-brand-600 text-[11px] font-black uppercase rounded-lg hover:bg-brand-50 transition-all flex items-center gap-2 shadow-sm dark:bg-white/[0.03] dark:border-gray-800 dark:text-brand-400 dark:hover:bg-brand-500/10">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Tambah Baris
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/30 dark:bg-white/[0.02]">
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Akun (Kode - Nama)</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500 w-32 border-l border-gray-50 dark:text-gray-400 dark:border-gray-800">Debit</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500 w-32 border-l border-gray-50 dark:text-gray-400 dark:border-gray-800">Kredit</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500 border-l border-gray-50 dark:text-gray-400 dark:border-gray-800">Keterangan Baris</th>
                                @if(!$isViewOnly)
                                    <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-gray-500 text-right w-12 dark:text-gray-400"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @foreach($entries as $index => $row)
                                <tr class="group hover:bg-gray-50/50 transition-all dark:hover:bg-white/[0.03]">
                                    <td class="px-4 py-3">
                                        <select wire:model="entries.{{ $index }}.account_id" {{ $isViewOnly ? 'disabled' : '' }} class="block w-full px-3 py-2 border-none rounded-lg bg-transparent text-sm font-bold text-gray-900 focus:ring-2 focus:bg-white transition-all disabled:opacity-80 dark:text-white dark:focus:bg-white/[0.05]
                                            @error("entries.{$index}.account_id") ring-2 ring-red-500 @else focus:ring-brand-500 @enderror">
                                            <option value="">-- Pilih Akun --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("entries.{$index}.account_id")
                                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3 bg-blue-50/20 border-l border-gray-50 dark:bg-blue-500/5 dark:border-gray-800">
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] font-black text-blue-300 dark:text-blue-400/60">Rp</span>
                                            <input type="number" wire:model.live.debounce.500ms="entries.{{ $index }}.debit" {{ $isViewOnly ? 'disabled' : '' }} class="block w-full p-2 border-none bg-transparent text-sm font-black text-right focus:outline-none focus:ring-0 disabled:opacity-80 dark:text-white">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 bg-amber-50/20 border-l border-gray-50 dark:bg-amber-500/5 dark:border-gray-800">
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] font-black text-amber-300 dark:text-amber-400/60">Rp</span>
                                            <input type="number" wire:model.live.debounce.500ms="entries.{{ $index }}.credit" {{ $isViewOnly ? 'disabled' : '' }} class="block w-full p-2 border-none bg-transparent text-sm font-black text-right focus:outline-none focus:ring-0 disabled:opacity-80 dark:text-white">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 border-l border-gray-50 dark:border-gray-800">
                                        <input type="text" wire:model="entries.{{ $index }}.description" {{ $isViewOnly ? 'disabled' : '' }} placeholder="Ket. per baris (opsional)" class="block w-full p-2 border-none bg-transparent text-sm text-gray-600 focus:outline-none focus:ring-0 disabled:opacity-80 italic dark:text-gray-300">
                                    </td>
                                    @if(!$isViewOnly)
                                        <td class="px-4 py-3 text-right">
                                            @if(count($entries) > 2)
                                                <button wire:click="removeEntry({{ $index }})" class="p-1.5 text-red-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all dark:text-red-400/60 dark:hover:bg-red-500/10">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
