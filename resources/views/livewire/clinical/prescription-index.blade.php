<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Antrian Resep (Clinical Queue)</h2>
            <p class="text-sm text-slate-500">Daftar resep pasien dari poliklinik dan ruangan.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('clinical.prescriptions.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-200 transition-all flex items-center gap-2">
                <i class="ph ph-plus-circle"></i>
                Input Resep Baru
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Cari No. Resep, Nama Pasien, No. RM...">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <i class="ph ph-magnifying-glass"></i>
                </div>
            </div>
        </div>
        <div class="w-48">
            <select wire:model.live="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Status</option>
                <option value="submitted">Submitted (Antri)</option>
                <option value="processing">Sedang Diracik</option>
                <option value="completed">Selesai / Diambil</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>
        <div class="w-48">
            <select wire:model.live="warehouse_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Apotek</option>
                @foreach($pharmacies as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <select wire:model.live="service_unit_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Poli/Unit</option>
                @foreach($serviceUnits as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 border-b border-slate-100 uppercase tracking-wider">
                        <th class="px-6 py-4">Informasi Resep</th>
                        <th class="px-6 py-4">Pasien & Dr. Pengirim</th>
                        <th class="px-6 py-4">Apotek Tujuan</th>
                        <th class="px-6 py-4">Waktu Input</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($prescriptions as $rx)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $rx->prescription_number }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">RM: {{ $rx->medical_record_number ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium">
                                <div class="text-slate-700">{{ $rx->patient_name }}</div>
                                <div class="text-[11px] text-slate-400 italic">Oleh: {{ $rx->doctor_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 uppercase">
                                    {{ $rx->warehouse->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $rx->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($rx->status) {
                                        'submitted' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                        'processing' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'cancelled' => 'bg-slate-50 text-slate-400 border-slate-100',
                                        default => 'bg-slate-50 text-slate-600 border-slate-100'
                                    };
                                    $statusLabel = match($rx->status) {
                                        'submitted' => 'QUEUED',
                                        'processing' => 'PREPARING',
                                        'completed' => 'DISPENSED',
                                        'cancelled' => 'CANCELLED',
                                        default => strtoupper($rx->status)
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[10px] font-bold border rounded {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @if($rx->status === 'submitted')
                                        <a href="{{ route('clinical.prescriptions.dispense', $rx->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Racik Obat">
                                            <i class="ph ph-pill text-lg"></i>
                                        </a>
                                    @endif
                                    <button class="p-2 text-slate-400 hover:bg-slate-50 rounded-lg transition-all">
                                        <i class="ph ph-printer text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                                Tidak ada resep ditemukan dengan kriteria tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($prescriptions->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>
</div>
