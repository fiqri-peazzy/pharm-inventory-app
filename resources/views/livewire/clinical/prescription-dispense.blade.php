<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-white/[0.03] dark:border-gray-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm shadow-indigo-50 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20 dark:shadow-none">
                <i class="ph-fill ph-pill text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Dispensing & Peracikan Obat</h2>
                <p class="text-sm text-slate-500 dark:text-gray-400">Resep No: <strong>{{ $prescription->prescription_number }}</strong> | Pasien: <strong class="text-slate-700 underline decoration-indigo-200 dark:text-gray-300">{{ $prescription->patient_name }}</strong></p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('clinical.prescriptions.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors dark:text-gray-400 dark:bg-white/[0.03] dark:hover:bg-white/[0.06]">
                Batal
            </a>
            <button wire:click="processDispense" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-lg shadow-indigo-200 transition-all flex items-center gap-2 transform hover:-translate-y-0.5 active:translate-y-0 dark:shadow-none">
                <i class="ph-fill ph-check-circle"></i>
                Selesaikan Dispensing
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Prescription Details -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center dark:border-gray-800 dark:bg-white/[0.02]">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2 uppercase tracking-wider text-xs dark:text-white">
                        <i class="ph-bold ph-list-bullets text-indigo-500"></i>
                        Item yang Harus Disiapkan
                    </h3>
                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20">FEFO-AUTO SUGGEST ENABLED</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 border-b border-slate-100 uppercase tracking-wider dark:bg-white/[0.02] dark:text-gray-400 dark:border-gray-800">
                                <th class="px-6 py-4">Nama Obat & Intruksi</th>
                                <th class="px-6 py-4 text-center">Qty Resep</th>
                                <th class="px-6 py-4">Pemilihan Batch (FIFO/FEFO)</th>
                                <th class="px-6 py-4">Status Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm dark:divide-gray-800">
                            @foreach($details as $index => $row)
                                <tr class="hover:bg-slate-50/50 transition-colors dark:hover:bg-white/[0.03]">
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-slate-800 text-base leading-tight dark:text-white">{{ $row['item_name'] }}</div>
                                        <div class="mt-1 flex items-center gap-1 text-indigo-600 bg-indigo-50/50 w-fit px-2 py-0.5 rounded border border-indigo-100/50 italic text-[11px] dark:text-indigo-400 dark:bg-indigo-500/10 dark:border-indigo-500/20">
                                            <i class="ph ph-info"></i> SIG: {{ $row['instruction'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 border-2 border-slate-200 text-lg font-black text-slate-700 dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-300">
                                            {{ $row['qty_prescribed'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 min-w-[300px]">
                                        <select wire:model.live="details.{{ $index }}.batch_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 font-medium dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                                            <option value="">-- Pilih Batch --</option>
                                            @foreach($row['available_batches'] as $batch)
                                                <option value="{{ $batch['id'] }}">
                                                    {{ $batch['batch_number'] }} (Exp: {{ date('d/m/y', strtotime($batch['expired_date'])) }}) | Stok: {{ $batch['current_qty'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($row['batch_id'])
                                            <div class="mt-2 flex items-center gap-3 text-[10px]">
                                                <span class="flex items-center gap-1 text-slate-500 dark:text-gray-400">
                                                    <i class="ph ph-database"></i> Sisa Stok: {{ $row['selected_batch_qty'] }}
                                                </span>
                                                <span class="flex items-center gap-1 {{ $row['selected_batch_qty'] < $row['qty_prescribed'] ? 'text-red-500 dark:text-red-400' : 'text-emerald-500 dark:text-emerald-400' }} font-bold">
                                                    <i class="ph {{ $row['selected_batch_qty'] < $row['qty_prescribed'] ? 'ph-x-circle' : 'ph-check-circle' }}"></i>
                                                    {{ $row['selected_batch_qty'] < $row['qty_prescribed'] ? 'Stok Tidak Cukup' : 'Siap Dispense' }}
                                                </span>
                                            </div>
                                            @if($row['batch_id'] != $row['fefo_batch_id'])
                                                <div class="mt-2 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-lg p-2.5">
                                                    <p class="flex items-center gap-1.5 text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wide mb-1.5">
                                                        <i class="ph-bold ph-warning-circle"></i> Bukan batch FEFO (kadaluarsa terdekat)
                                                    </p>
                                                    <input type="text" wire:model.live="details.{{ $index }}.override_reason"
                                                        placeholder="Wajib isi alasan, mis: batch FEFO sedang dikarantina"
                                                        class="w-full px-2.5 py-1.5 text-[11px] bg-white dark:bg-white/[0.03] border border-amber-200 dark:border-amber-500/30 rounded-md focus:ring-1 focus:ring-amber-500 dark:text-white">
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        @if(!$row['batch_id'])
                                            <span class="flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded border border-red-100 uppercase italic dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20">
                                                <i class="ph-bold ph-warning"></i> Belum Dipilih
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100 uppercase italic dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20">
                                                <i class="ph-bold ph-check"></i> Sudah Valid
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar / Summary -->
        <div class="space-y-6">
            <div class="bg-indigo-600 p-6 rounded-2xl text-white shadow-xl shadow-indigo-100 border border-indigo-500 dark:shadow-none">
                <h4 class="text-xs font-bold text-indigo-100 uppercase mb-4 tracking-widest">Ringkasan Resep</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-end border-b border-indigo-500/50 pb-2">
                        <span class="text-xs text-indigo-200">Total Item</span>
                        <span class="text-2xl font-black italic">{{ count($details) }}</span>
                    </div>
                    <div class="flex justify-between items-end border-b border-indigo-500/50 pb-2">
                        <span class="text-xs text-indigo-200">Status Pasien</span>
                        <span class="text-sm font-bold">Umum / BPJS</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs text-indigo-200">Unit Asal</span>
                        <span class="text-xs font-bold">{{ $prescription->serviceUnit->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Etiket Preview -->
            <div class="bg-white p-5 rounded-xl border border-dashed border-slate-300 dark:bg-white/[0.03] dark:border-gray-700">
                <h4 class="text-xs font-bold text-slate-500 uppercase mb-4 flex items-center gap-2 dark:text-gray-400">
                    <i class="ph ph-printer"></i> Preview Etiket Obat
                </h4>
                <div class="bg-indigo-50/30 border border-slate-200 p-4 rounded-lg transform rotate-1 scale-95 origin-top opacity-60 dark:bg-indigo-500/5 dark:border-gray-800">
                    <div class="text-[10px] font-black border-b border-slate-200 pb-1 mb-2 dark:border-gray-800 dark:text-white">RSUD SMART - SIMRS NF</div>
                    <div class="text-[9px] mb-1 dark:text-gray-300">Pasien: {{ $prescription->patient_name }}</div>
                    <div class="text-[10px] font-bold h-8 flex items-center italic dark:text-gray-300">... Aturan Pakai Obat ...</div>
                    <div class="text-[8px] text-right text-slate-400 mt-2 italic dark:text-gray-500">{{ date('d/m/y H:i') }}</div>
                </div>
                <p class="text-[10px] text-slate-400 mt-4 italic text-center leading-tight dark:text-gray-500">
                    Etiket akan dicetak otomatis setelah tombol "Selesaikan Dispensing" diklik.
                </p>
            </div>
        </div>
    </div>
</div>
