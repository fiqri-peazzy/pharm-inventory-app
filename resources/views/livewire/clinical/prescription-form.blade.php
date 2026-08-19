<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-white/[0.03] dark:border-gray-800">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Input Resep Pasien</h2>
            <p class="text-sm text-slate-500 dark:text-gray-400">Unit:
                <strong>{{ \App\Models\ServiceUnit::find($service_unit_id)->name ?? '-' }}</strong> | No. Resep:
                <strong>{{ $prescription_number }}</strong>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('clinical.prescriptions.index') }}"
                class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors dark:text-gray-400 dark:bg-white/[0.03] dark:hover:bg-white/[0.06]">
                Batal
            </a>
            <button wire:click="save"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-200 transition-all flex items-center gap-2 dark:shadow-none">
                <i class="ph ph-paper-plane-tilt"></i>
                Kirim ke Apotek
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Patient Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 dark:bg-white/[0.03] dark:border-gray-800">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2 dark:text-white">
                    <i class="ph ph-user-circle text-indigo-500"></i>
                    Informasi Pasien
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 dark:text-gray-400">Nama Pasien</label>
                        <input type="text" wire:model="patient_name"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white"
                            placeholder="Input nama lengkap...">
                        @error('patient_name') <span class="text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 dark:text-gray-400">No. RM
                            (Opsional)</label>
                        <input type="text" wire:model="medical_record_number"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white"
                            placeholder="00-00-00">
                    </div>

                    <!-- Payer Type -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 dark:text-gray-400">Tipe Penjamin</label>
                        <select wire:model.live="payer_type"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                            <option value="umum">Umum (Bayar Sendiri)</option>
                            <option value="bpjs">BPJS</option>
                            <option value="asuransi_lain">Asuransi Lain</option>
                        </select>
                        @error('payer_type') <span class="text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
                        @if($payer_type === 'bpjs')
                            <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-2 dark:bg-blue-500/10 dark:border-blue-500/20">
                                <i class="ph ph-info text-blue-600 text-sm mt-0.5 dark:text-blue-400"></i>
                                <p class="text-[10px] text-blue-700 leading-relaxed dark:text-blue-300">
                                    <strong>FORNAS Only:</strong> Pasien BPJS hanya dapat menerima obat yang terdaftar dalam
                                    FORNAS (Formularium Nasional).
                                </p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 dark:text-gray-400">Unit Layanan
                            (Poli/Ruang)</label>
                        <select wire:model.live="service_unit_id"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                            @foreach($serviceUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('service_unit_id') <span class="text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <!-- Patient Type (Auto-detected) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 dark:text-gray-400">Tipe Pasien</label>
                        <div
                            class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg flex items-center gap-2 dark:bg-white/[0.03] dark:border-gray-800">
                            @if($patient_type === 'ri')
                                <span
                                    class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded border border-purple-200 dark:bg-purple-500/15 dark:text-purple-400 dark:border-purple-500/20">
                                    <i class="ph ph-bed"></i> RAWAT INAP (RI)
                                </span>
                            @else
                                <span
                                    class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded border border-green-200 dark:bg-green-500/15 dark:text-green-400 dark:border-green-500/20">
                                    <i class="ph ph-user-check"></i> RAWAT JALAN (RJ)
                                </span>
                            @endif
                            <span class="text-[10px] text-slate-500 italic ml-auto dark:text-gray-400">Auto-detected</span>
                        </div>
                    </div>

                    <!-- Room/Bed Number (Only for RI) -->
                    @if($patient_type === 'ri')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 dark:text-gray-400">No. Kamar / Bed</label>
                            <input type="text" wire:model="room_bed_number"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white"
                                placeholder="Contoh: 201-A">
                            @error('room_bed_number') <span class="text-xs text-red-500 dark:text-red-400">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 dark:text-gray-400">Apotek Tujuan</label>
                        <select wire:model.live="warehouse_id"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white">
                            @foreach($pharmacies as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Zero-OOS Stock Legend -->
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 dark:bg-indigo-500/10 dark:border-indigo-500/20">
                <h4 class="text-xs font-bold text-indigo-900 uppercase mb-2 flex items-center gap-1 dark:text-indigo-300">
                    <i class="ph ph-shield-check"></i>
                    Sistem Kontrol Stok (Zero-OOS)
                </h4>
                <p class="text-xs text-indigo-700 leading-relaxed mb-3 dark:text-indigo-300">
                    Stok dicek secara real-time di
                    <strong>{{ \App\Models\Warehouse::find($warehouse_id)->name ?? 'Apotek' }}</strong> untuk mencegah
                    resep obat kosong.
                </p>
                <div class="space-y-2">
                    <div
                        class="flex items-center gap-2 text-[10px] font-medium text-indigo-800 uppercase trackers-wider dark:text-indigo-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tersedia
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Stok Menipis
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Stok Kosong
                    </div>
                </div>
            </div>
        </div>

        <!-- Drug Entry -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center dark:border-gray-800 dark:bg-white/[0.02]">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2 dark:text-white">
                        <i class="ph ph-pill text-indigo-500"></i>
                        Daftar Obat (Resep)
                    </h3>
                    <button @click="$dispatch('open-item-modal')"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 p-1 px-2 rounded-md hover:bg-indigo-50 transition-all dark:text-indigo-400 dark:hover:bg-indigo-500/10">
                        <i class="ph ph-plus-circle"></i> Tambah Obat
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50/50 text-[10px] font-bold text-slate-500 border-b border-slate-100 uppercase tracking-wider dark:bg-white/[0.02] dark:text-gray-400 dark:border-gray-800">
                                <th class="px-4 py-3">Nama Obat</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3">Aturan Pakai / Instruksi</th>
                                <th class="px-4 py-3 w-10 text-center text-slate-400 dark:text-gray-500"><i class="ph ph-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm dark:divide-gray-800">
                            @forelse($rows as $index => $row)
                                <tr class="hover:bg-slate-50/50 transition-colors dark:hover:bg-white/[0.03]">
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ $row['item_name'] }}</div>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            @if($row['stock_status'] === 'out_of_stock')
                                                <span
                                                    class="flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100 dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20">
                                                    <i class="ph-fill ph-warning"></i> KOSONG
                                                </span>
                                            @elseif($row['stock_status'] === 'low_stock')
                                                <span
                                                    class="flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/20">
                                                    <i class="ph-fill ph-arrows-down"></i> KRITIS
                                                    ({{ $row['available_stock'] }})
                                                </span>
                                            @else
                                                <span
                                                    class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20">
                                                    <i class="ph-fill ph-check-circle"></i> TERSEDIA
                                                    ({{ $row['available_stock'] }})
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 w-24">
                                        <input type="number" wire:model="rows.{{ $index }}.qty"
                                            class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-md text-center font-bold focus:ring-indigo-500 focus:border-indigo-500 dark:bg-white/[0.03] dark:border-gray-800 dark:text-white"
                                            step="0.1">
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="text" wire:model="rows.{{ $index }}.instruction"
                                            list="dosage-instructions-{{ $index }}"
                                            class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-md text-slate-600 focus:ring-indigo-500 focus:border-indigo-500 italic dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-300"
                                            placeholder="Ketik atau pilih aturan pakai...">
                                        <datalist id="dosage-instructions-{{ $index }}">
                                            @foreach($dosageInstructions as $di)
                                                <option value="{{ $di->instruction }}">
                                                    {{ $di->code }} - {{ $di->frequency }} {{ $di->timing_name }}
                                                </option>
                                            @endforeach
                                        </datalist>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <button wire:click="removeRow({{ $index }})"
                                            class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all dark:text-gray-500 dark:hover:bg-red-500/10 dark:hover:text-red-400">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-slate-400 italic dark:text-gray-500">
                                        Belum ada obat yang dipilih. Klik "Tambah Obat" untuk memulai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Search Modal -->
    <div x-data="{ open: false }" @open-item-modal.window="open = true" @close-item-modal.window="open = false"
        x-show="open" class="fixed inset-0 z-[1000000] overflow-y-auto font-sans" style="display: none;">

        <!-- Backdrop -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
            class="fixed inset-0 bg-slate-900/35 backdrop-blur-[2px] transition-opacity"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4" :class="{ 'xl:pl-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered, 'xl:pl-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered }">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all dark:bg-gray-900 dark:border-gray-800">

                <div
                    class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center text-sm uppercase font-bold tracking-widest text-slate-500 dark:bg-white/[0.02] dark:border-gray-800 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-magnifying-glass text-indigo-500"></i>
                        PENCARIAN OBAT / BMHP
                    </div>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 dark:text-gray-500 dark:hover:text-gray-300">
                        <i class="ph ph-x-circle text-2xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="itemSearch"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 shadow-inner dark:bg-white/[0.03] dark:border-gray-800 dark:text-white"
                            placeholder="Cari nama obat atau kode barang...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl dark:text-gray-500">
                            <i class="ph ph-magnifying-glass font-bold"></i>
                        </div>
                    </div>

                    <div
                        class="max-h-[400px] overflow-y-auto rounded-xl border border-slate-100 divide-y divide-slate-100 bg-white dark:border-gray-800 dark:divide-gray-800 dark:bg-transparent">
                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="itemSearch" class="p-16 text-center">
                            <i class="ph ph-circle-notch ph-spin text-4xl text-indigo-500"></i>
                            <p class="text-xs text-slate-400 mt-4 uppercase font-bold tracking-[0.2em] dark:text-gray-500">Mencari Data...
                            </p>
                        </div>

                        <!-- Search Results -->
                        <div wire:loading.remove wire:target="itemSearch">
                            @forelse($searchResults as $item)
                                <button wire:click="selectItem({{ $item->id }})"
                                    class="w-full flex items-center justify-between p-4 hover:bg-slate-50 transition-all text-left group dark:hover:bg-white/[0.03]">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black text-lg border border-indigo-100 shadow-sm shadow-indigo-50 dark:bg-indigo-500/15 dark:text-indigo-400 dark:border-indigo-500/20 dark:shadow-none">
                                            {{ substr($item->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 group-hover:text-indigo-700 text-base dark:text-white dark:group-hover:text-indigo-400">
                                                {{ $item->name }}
                                            </div>
                                            <div
                                                class="text-[10px] text-slate-500 uppercase font-black tracking-widest mt-1 dark:text-gray-400">
                                                {{ $item->code }} <span class="mx-1 text-slate-300 dark:text-gray-600">|</span>
                                                {{ $item->category->name ?? 'GENERAL' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-[10px] font-black text-indigo-600 bg-white border border-indigo-200 px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all uppercase tracking-[0.1em] shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 dark:bg-white/[0.03] dark:text-indigo-400 dark:border-indigo-500/30">
                                        PILIH ITEM
                                    </div>
                                </button>
                            @empty
                                <div class="p-16 text-center text-slate-300 dark:text-gray-600">
                                    @if(strlen($itemSearch) >= 2)
                                        <i class="ph ph-warning-circle text-5xl opacity-20"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest mt-4">Maaf, item tidak
                                            ditemukan</p>
                                    @else
                                        <i class="ph ph-magnifying-glass text-5xl opacity-20"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest mt-4">Ketik nama obat untuk
                                            mencari</p>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
