<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-white/[0.03]">
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
    <div class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-white/[0.03]">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Cari No. Resep, Nama Pasien, No. RM...">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <i class="ph ph-magnifying-glass"></i>
                </div>
            </div>
        </div>
        <div class="w-40">
            <select wire:model.live="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Status</option>
                <option value="submitted">Submitted (Antri)</option>
                <option value="processing">Sedang Diracik</option>
                <option value="completed">Selesai / Diambil</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>
        <div class="w-40">
            <select wire:model.live="payer_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Penjamin</option>
                <option value="umum">Umum</option>
                <option value="bpjs">BPJS</option>
                <option value="asuransi_lain">Asuransi Lain</option>
            </select>
        </div>
        <div class="w-32">
            <select wire:model.live="patient_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">RJ & RI</option>
                <option value="rj">RJ Only</option>
                <option value="ri">RI Only</option>
            </select>
        </div>
        <div class="w-40">
            <select wire:model.live="payment_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Semua Pembayaran</option>
                <option value="unpaid">Belum Bayar</option>
                <option value="partial">Bayar Sebagian</option>
                <option value="paid">Lunas</option>
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
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 border-b border-slate-100 uppercase tracking-wider">
                        <th class="px-6 py-4">Informasi Resep</th>
                        <th class="px-6 py-4">Pasien & Dr. Pengirim</th>
                        <th class="px-6 py-4">Tipe & Penjamin</th>
                        <th class="px-6 py-4">Apotek Tujuan</th>
                        <th class="px-6 py-4">Waktu Input</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($prescriptions as $rx)
                        <tr wire:key="rx-{{ $rx->id }}" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $rx->prescription_number }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">RM: {{ $rx->medical_record_number ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium">
                                <div class="text-slate-700">{{ $rx->patient_name }}</div>
                                <div class="text-[11px] text-slate-400 italic">Oleh: {{ $rx->doctor_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    @php
                                        $patientTypeColor = $rx->patient_type === 'ri' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-green-100 text-green-700 border-green-200';
                                        $patientTypeIcon = $rx->patient_type === 'ri' ? 'ph-bed' : 'ph-user-check';
                                        $patientTypeLabel = $rx->patient_type === 'ri' ? 'RI' : 'RJ';
                                        
                                        $payerTypeColor = match($rx->payer_type) {
                                            'bpjs' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'asuransi_lain' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200'
                                        };
                                        $payerTypeLabel = match($rx->payer_type) {
                                            'bpjs' => 'BPJS',
                                            'asuransi_lain' => 'Asuransi',
                                            default => 'Umum'
                                        };
                                        
                                        $paymentColor = match($rx->payment_status) {
                                            'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'partial' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            default => 'bg-red-100 text-red-700 border-red-200'
                                        };
                                        $paymentIcon = match($rx->payment_status) {
                                            'paid' => 'ph-check-circle',
                                            'partial' => 'ph-warning',
                                            default => 'ph-x-circle'
                                        };
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 text-[10px] font-bold border rounded {{ $patientTypeColor }} flex items-center gap-1">
                                            <i class="{{ $patientTypeIcon }}"></i> {{ $patientTypeLabel }}
                                        </span>
                                        @if($rx->patient_type === 'ri' && $rx->room_bed_number)
                                            <span class="text-[9px] text-purple-600 font-medium">{{ $rx->room_bed_number }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 text-[10px] font-bold border rounded {{ $payerTypeColor }}">
                                            {{ $payerTypeLabel }}
                                        </span>
                                        @if($rx->payer_type === 'umum')
                                            @php
                                                $paymentLabel = match($rx->payment_status) {
                                                    'paid' => 'Lunas',
                                                    'partial' => 'Sebagian',
                                                    default => 'Belum'
                                                };
                                            @endphp
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold border rounded {{ $paymentColor }} flex items-center gap-0.5">
                                                <i class="{{ $paymentIcon }}"></i> {{ $paymentLabel }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
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
                                    @if($rx->status === 'submitted' || $rx->status === 'processing')
                                        <a href="{{ route('clinical.prescriptions.dispense', $rx->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all group" title="{{ $rx->status === 'processing' ? 'Lanjutkan Dispensing' : 'Proses Dispensing / Racik' }}">
                                            <i class="ph ph-pill text-lg group-hover:scale-110 transition-transform"></i>
                                        </a>
                                    @endif
                                    
                                    @if($rx->status === 'submitted')
                                        <a href="{{ route('clinical.prescriptions.edit', $rx->id) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-all group" title="Edit Resep">
                                            <i class="ph ph-note-pencil text-lg group-hover:scale-110 transition-transform"></i>
                                        </a>
                                    @endif

                                    @if($rx->status === 'completed')
                                        <button wire:click="printEtiket({{ $rx->id }})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all group" title="Cetak Etiket Obat">
                                            <i class="ph ph-identification-card text-lg group-hover:scale-110 transition-transform"></i>
                                        </button>
                                        <button wire:click="printPrescription({{ $rx->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all group" title="Cetak Salinan Resep">
                                            <i class="ph ph-printer text-lg group-hover:scale-110 transition-transform"></i>
                                        </button>
                                    @endif

                                    <button wire:click="viewDetails({{ $rx->id }})" class="p-2 text-slate-400 hover:bg-slate-100 rounded-lg transition-all group" title="Lihat Detail Resep">
                                        <i class="ph ph-eye text-lg group-hover:scale-110 transition-transform"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
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

    <!-- Modals -->
    @if($showDetailModal && $selectedPrescription)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/35 backdrop-blur-[2px]" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }" wire:click.self="closeModals">
            <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 dark:bg-gray-900">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50 dark:border-gray-800 dark:bg-white/[0.02]">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Detail Resep: {{ $selectedPrescription->prescription_number }}</h3>
                        <p class="text-xs text-slate-500 dark:text-gray-400">Pasien: {{ $selectedPrescription->patient_name }} ({{ $selectedPrescription->medical_record_number }})</p>
                    </div>
                    <button wire:click="closeModals" class="p-2 hover:bg-slate-200 rounded-full transition-colors dark:hover:bg-gray-800 dark:text-gray-400">
                        <i class="ph ph-x font-bold"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 dark:text-gray-500">Dokter Pengirim</p>
                            <p class="font-bold text-slate-700 dark:text-gray-300">{{ $selectedPrescription->doctor_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 dark:text-gray-500">Unit/Poli Asal</p>
                            <p class="font-bold text-slate-700 dark:text-gray-300">{{ $selectedPrescription->serviceUnit->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <table class="w-full text-left border-collapse rounded-xl overflow-hidden border border-slate-100 dark:border-gray-800">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider dark:bg-white/[0.02] dark:text-gray-400">
                                    <th class="px-4 py-3">Nama Obat</th>
                                    <th class="px-4 py-3 text-center">Jumlah</th>
                                    <th class="px-4 py-3">Instruksi/Sig</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs dark:divide-gray-800">
                                @foreach($selectedPrescription->details as $detail)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.03]">
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-slate-800 dark:text-white">{{ $detail->item->name }}</div>
                                            <div class="text-[10px] text-slate-400 dark:text-gray-500">{{ $detail->item->code }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center font-black text-slate-700 dark:text-gray-300">{{ $detail->qty }}</td>
                                        <td class="px-4 py-3 italic text-indigo-600 font-medium dark:text-indigo-400">{{ $detail->instruction }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 dark:bg-white/[0.02] dark:border-gray-800">
                    <button wire:click="closeModals" class="px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-200 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-gray-800">Tutup</button>
                    @if($selectedPrescription->status === 'completed')
                        <button wire:click="printPrescription({{ $selectedPrescription->id }})" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-md transition-all flex items-center gap-2 dark:shadow-none">
                            <i class="ph-fill ph-printer"></i> Cetak Resep
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($showEtiketModal && $selectedPrescription)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/35 backdrop-blur-[2px]" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }" wire:click.self="closeModals">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-200 dark:bg-gray-900">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-emerald-50 text-emerald-700 dark:border-gray-800 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <h3 class="font-bold flex items-center gap-2">
                        <i class="ph ph-tag"></i> Preview Etiket Obat
                    </h3>
                    <button wire:click="closeModals" class="p-1 hover:bg-emerald-100 rounded-full transition-colors dark:hover:bg-emerald-500/20">
                        <i class="ph ph-x font-bold"></i>
                    </button>
                </div>
                <div class="p-8 bg-slate-50 flex flex-col items-center gap-6 dark:bg-white/[0.02]">
                    <!-- Virtual Labels -->
                    @foreach($selectedPrescription->details as $detail)
                        @php
                            $isRI = $selectedPrescription->patient_type === 'ri';
                            $borderColor = $isRI ? 'border-purple-200 hover:border-purple-500' : 'border-emerald-200 hover:border-emerald-500';
                            $bgColor = $isRI ? 'bg-purple-50' : 'bg-emerald-50';
                            $textColor = $isRI ? 'text-purple-800' : 'text-emerald-800';
                            $textColorLight = $isRI ? 'text-purple-600' : 'text-emerald-600';
                            $borderColorInner = $isRI ? 'border-purple-100' : 'border-emerald-100';
                        @endphp
                        <div class="bg-white border-2 {{ $borderColor }} w-full p-4 rounded-lg shadow-sm border-dashed relative overflow-hidden group transition-colors cursor-pointer dark:bg-white/[0.03]">
                            <div class="text-[10px] font-black border-b border-slate-100 pb-1 mb-2 text-slate-400 dark:border-gray-800 dark:text-gray-500">RSUD SMART - SIMRS NF</div>
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="text-xs font-bold text-slate-800 dark:text-white">{{ $selectedPrescription->patient_name }}</div>
                                    <div class="text-[9px] text-slate-400 dark:text-gray-500">Tgl: {{ date('d/m/Y') }}</div>
                                    @if($isRI && $selectedPrescription->room_bed_number)
                                        <div class="text-[9px] font-bold text-purple-600 mt-0.5 flex items-center gap-1">
                                            <i class="ph ph-bed"></i> Kamar: {{ $selectedPrescription->room_bed_number }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="text-[10px] font-black bg-slate-100 px-1.5 py-0.5 rounded dark:bg-white/[0.06] dark:text-gray-300">{{ $selectedPrescription->prescription_number }}</div>
                                    @if($isRI)
                                        <div class="text-[8px] font-bold bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded border border-purple-200 flex items-center gap-0.5 dark:bg-purple-500/15 dark:text-purple-400 dark:border-purple-500/20">
                                            <i class="ph ph-bed"></i> RAWAT INAP
                                        </div>
                                    @else
                                        <div class="text-[8px] font-bold bg-green-100 text-green-700 px-1.5 py-0.5 rounded border border-green-200 flex items-center gap-0.5 dark:bg-green-500/15 dark:text-green-400 dark:border-green-500/20">
                                            <i class="ph ph-user-check"></i> RAWAT JALAN
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="{{ $bgColor }} p-2 rounded border {{ $borderColorInner }} mb-2 dark:bg-white/[0.03]">
                                <div class="text-[11px] font-bold {{ $textColor }} uppercase dark:text-white">{{ $detail->item->name }}</div>
                                <div class="text-[10px] font-medium {{ $textColorLight }} italic dark:text-gray-400">SIG: {{ $detail->instruction }}</div>
                            </div>
                            <div class="text-[8px] text-right text-slate-300 italic dark:text-gray-600">Antigravity V.1</div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-slate-100 flex justify-center gap-3 dark:border-gray-800">
                    <button onclick="window.print()" class="w-full py-3 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2 dark:shadow-none">
                        <i class="ph-fill ph-printer"></i> Cetak Semua Etiket
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showPrintModal && $selectedPrescription)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/35 backdrop-blur-[2px]" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }" wire:click.self="closeModals">
            <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 dark:bg-gray-900">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-indigo-50 text-indigo-700 dark:border-gray-800 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <h3 class="font-bold flex items-center gap-2">
                        <i class="ph ph-printer"></i> Preview Salinan Resep
                    </h3>
                    <button wire:click="closeModals" class="p-1 hover:bg-indigo-100 rounded-full transition-colors dark:hover:bg-indigo-500/20">
                        <i class="ph ph-x font-bold"></i>
                    </button>
                </div>
                <div class="p-12 bg-white flex flex-col gap-6 font-mono text-sm leading-relaxed text-slate-800 dark:bg-white/[0.02] dark:text-gray-200">
                    <!-- Letterhead Placeholder -->
                    <div class="text-center border-b-2 border-slate-800 pb-4 mb-4 dark:border-gray-700">
                        <div class="text-lg font-black italic">RSUD SMART - SIMRS NF</div>
                        <div class="text-[10px]">Jl. Contoh Alamat No. 123, Kabupaten/Kota, Indonesia</div>
                    </div>

                    <div class="flex justify-between items-start border-b border-slate-100 pb-4 dark:border-gray-800">
                        <div class="space-y-1">
                            <div class="text-[10px] uppercase font-bold text-slate-400 dark:text-gray-500">PASIEN</div>
                            <div class="font-black">{{ $selectedPrescription->patient_name }}</div>
                            <div class="text-xs">RM: {{ $selectedPrescription->medical_record_number }}</div>
                        </div>
                        <div class="text-right space-y-1">
                            <div class="text-[10px] uppercase font-bold text-slate-400 dark:text-gray-500">NO. RESEP</div>
                            <div class="font-black">{{ $selectedPrescription->prescription_number }}</div>
                            <div class="text-xs">{{ $selectedPrescription->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <div class="py-4 min-h-[200px]">
                        @foreach($selectedPrescription->details as $detail)
                            <div class="mb-4">
                                <span class="font-black text-lg">R/</span>
                                <span class="ml-4 font-bold">{{ $detail->item->name }}</span>
                                <span class="ml-2">No. ( {{ $detail->qty }} )</span>
                                <div class="ml-10 italic text-slate-600 font-medium dark:text-gray-400">S . {{ $detail->instruction }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end">
                        <div class="text-center w-48">
                            <div class="text-xs mb-12">Dokter Pemeriksa,</div>
                            <div class="font-bold border-b border-slate-800 pb-1 dark:border-gray-600">( {{ $selectedPrescription->doctor_name }} )</div>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-center gap-3 dark:bg-white/[0.02] dark:border-gray-800">
                    <button onclick="window.print()" class="w-full py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 dark:shadow-none">
                        <i class="ph-fill ph-printer"></i> Cetak Sekarang (PDF)
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
