<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 relative overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <i class="ph-bold ph-trash text-8xl text-rose-600"></i>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
            <div>
                <a href="{{ route('inventory.disposals.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 mb-2 group transition-all dark:text-rose-400 dark:hover:text-rose-300">
                    <i class="ph-bold ph-arrow-left transition-transform group-hover:-translate-x-1"></i> KEMBALI KE DAFTAR
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight italic dark:text-white">
                    {{ strtoupper($type === 'disposal' ? 'PEMUSNAHAN BARANG' : 'RETUR KE SUPPLIER') }}
                </h1>
                <p class="text-slate-500 text-sm font-medium uppercase tracking-widest mt-1 dark:text-gray-400">
                    {{ $disposal_number }} •
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black tracking-tighter
                        {{ $status === 'draft' ? 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400' : '' }}
                        {{ $status === 'submitted' ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400' : '' }}
                        {{ $status === 'approved' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400' : '' }}
                        {{ $status === 'executed' ? 'bg-purple-100 text-purple-600 dark:bg-purple-500/15 dark:text-purple-400' : '' }}
                        {{ $status === 'posted' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400' : '' }}
                    ">
                        {{ strtoupper($status) }}
                    </span>
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($status === 'draft' && !$isViewOnly)
                    <button wire:click="saveDraft" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 dark:bg-white/[0.05] dark:hover:bg-white/[0.1] dark:text-gray-200">
                        <i class="ph-bold ph-floppy-disk"></i> Simpan Draft
                    </button>
                    <button wire:click="submitForReview" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-slate-200 flex items-center gap-2 dark:shadow-none">
                        <i class="ph-bold ph-paper-plane-tilt"></i> Ajukan Review
                    </button>
                @endif

                @if($status === 'submitted')
                    @if(auth()->user()->hasRole(['super-admin', 'kepala-farmasi', 'direktur']))
                        <button wire:click="reject" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 dark:bg-rose-500/15 dark:hover:bg-rose-500/20 dark:text-rose-400">
                            <i class="ph-bold ph-x-circle"></i> Tolak (Draft)
                        </button>
                        <button wire:click="approve" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-200 flex items-center gap-2 dark:shadow-none">
                            <i class="ph-bold ph-check-circle"></i> Setujui (Approve)
                        </button>
                    @endif
                @endif

                @if($status === 'approved')
                    <button wire:click="markExecuted" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-purple-200 flex items-center gap-2 dark:shadow-none">
                        <i class="ph-bold ph-lightning"></i> Tandai Sudah Eksekusi Fisik
                    </button>
                @endif

                @if($status === 'executed')
                    <button wire:click="post" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-200 flex items-center gap-2 dark:shadow-none">
                        <i class="ph-bold ph-check-square"></i> POSTING KE STOK & AKUNTANSI
                    </button>
                @endif

                @if($status === 'posted')
                    <div class="bg-emerald-50 text-emerald-700 px-6 py-2.5 rounded-xl font-bold text-sm border border-emerald-100 flex items-center gap-2 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20">
                        <i class="ph-bold ph-check-square"></i> SELESAI (POSTED)
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left: Order Details --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Basic Info --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 dark:bg-white/[0.03] dark:border-gray-800">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2 uppercase tracking-tighter dark:text-white">
                    <i class="ph-bold ph-info text-rose-500"></i> INFORMASI DASAR
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Gudang / Depo</label>
                        <select wire:model.live="warehouse_id" {{ $isEdit || $isViewOnly || !empty($rows) ? 'disabled' : '' }} class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-rose-500 transition-all font-bold text-slate-700 dark:bg-white/[0.05] dark:text-gray-200 @if(!empty($rows)) opacity-50 @endif">
                            <option value="">Pilih Gudang...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @if(!empty($rows) && !$isViewOnly)
                            <p class="text-[9px] text-amber-600 font-bold mt-1 italic uppercase tracking-tighter dark:text-amber-400">* Gudang dikunci (item sudah ada)</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Tipe Disposal</label>
                        <select wire:model="disposal_type" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-rose-500 transition-all font-bold text-slate-700 dark:bg-white/[0.05] dark:text-gray-200">
                            <option value="">Pilih Tipe...</option>
                            <option value="Expired">Kadaluarsa (Expired)</option>
                            <option value="Damaged">Rusak (Damaged)</option>
                            <option value="Lost">Hilang (Lost/Theft)</option>
                            <option value="Recall">Ditarik (Recall)</option>
                            <option value="Others">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Tanggal Pengajuan</label>
                        <input type="date" wire:model="disposal_date" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-rose-500 transition-all font-bold text-slate-700 dark:bg-white/[0.05] dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Total Nilai (HPP)</label>
                        <div class="w-full bg-rose-50 border-none rounded-xl text-sm py-3 px-4 font-black text-rose-700 flex items-center justify-between dark:bg-rose-500/15 dark:text-rose-400">
                            <span>Rp</span>
                            <span>{{ number_format($total_value, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit Integration (Smart Load) --}}
            @if(!$isViewOnly)
                <div class="bg-rose-50/50 rounded-3xl border border-rose-100 p-6 dark:bg-rose-500/5 dark:border-rose-500/20">
                    <h3 class="text-sm font-black text-rose-800 mb-4 flex items-center gap-2 uppercase tracking-tighter dark:text-rose-400">
                        <i class="ph-bold ph-cpu text-rose-600"></i> INTEGRASI AUDIT
                    </h3>
                    <p class="text-[10px] text-rose-600 font-bold mb-4 uppercase leading-tight italic dark:text-rose-400">
                        Tarik barang dari hasil audit/perhitungan sebelumnya secara otomatis:
                    </p>
                    <div class="space-y-2">
                        <button wire:click="loadExpiredItems" class="w-full bg-white hover:bg-rose-600 hover:text-white text-rose-600 p-3 rounded-xl text-[10px] font-black uppercase tracking-widest border border-rose-200 transition-all flex items-center justify-between group shadow-sm dark:bg-white/[0.03] dark:border-rose-500/20 dark:text-rose-400">
                            <span>Barang Kadaluarsa</span>
                            <i class="ph-bold ph-calendar-x opacity-30 group-hover:opacity-100"></i>
                        </button>
                        <button wire:click="loadDamagedFromAdjustments" class="w-full bg-white hover:bg-rose-600 hover:text-white text-rose-600 p-3 rounded-xl text-[10px] font-black uppercase tracking-widest border border-rose-200 transition-all flex items-center justify-between group shadow-sm dark:bg-white/[0.03] dark:border-rose-500/20 dark:text-rose-400">
                            <span>Barang Rusak (Adjust)</span>
                            <i class="ph-bold ph-wrench opacity-30 group-hover:opacity-100"></i>
                        </button>
                        <button wire:click="loadDamagedFromOpname" class="w-full bg-white hover:bg-indigo-600 hover:text-white text-indigo-600 p-3 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-200 transition-all flex items-center justify-between group shadow-sm dark:bg-white/[0.03] dark:border-indigo-500/20 dark:text-indigo-400">
                            <span>Selisih Opname (Minus)</span>
                            <i class="ph-bold ph-clipboard-text opacity-30 group-hover:opacity-100"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Official Execution Details --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 dark:bg-white/[0.03] dark:border-gray-800">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2 uppercase tracking-tighter dark:text-white">
                    <i class="ph-bold ph-article text-indigo-500"></i> BERITA ACARA
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Nomor BA</label>
                        <input type="text" wire:model="ba_number" {{ $isViewOnly ? 'disabled' : '' }} placeholder="BA/DSP/2026/..." class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:bg-white/[0.05] dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Metode & Lokasi</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" wire:model="method" {{ $isViewOnly ? 'disabled' : '' }} placeholder="Metode..." class="w-full bg-slate-50 border-none rounded-xl text-[11px] py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:bg-white/[0.05] dark:text-gray-200">
                            <input type="text" wire:model="location" {{ $isViewOnly ? 'disabled' : '' }} placeholder="Lokasi..." class="w-full bg-slate-50 border-none rounded-xl text-[11px] py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:bg-white/[0.05] dark:text-gray-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Tanggal Eksekusi Fisik</label>
                        <input type="date" wire:model="execution_date" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:bg-white/[0.05] dark:text-gray-200">
                    </div>

                    {{-- Witnesses List --}}
                    <div class="pt-2 border-t border-slate-50 dark:border-gray-800">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest dark:text-gray-500">Saksi-Saksi</label>
                        <div class="space-y-2 mb-3">
                            @foreach($witnesses as $idx => $w)
                                <div class="bg-indigo-50/50 rounded-xl p-2.5 flex items-center justify-between group dark:bg-indigo-500/10">
                                    <div class="overflow-hidden">
                                        <p class="text-[10px] font-black text-indigo-800 uppercase truncate dark:text-indigo-300">{{ $w['name'] }}</p>
                                        <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-tighter dark:text-indigo-400">{{ $w['role'] }}</p>
                                    </div>
                                    @if(!$isViewOnly)
                                        <button wire:click="removeWitness({{ $idx }})" class="text-indigo-300 hover:text-rose-500 px-2 transition-all dark:text-indigo-500 dark:hover:text-rose-400">
                                            <i class="ph-bold ph-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if(!$isViewOnly)
                            <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 dark:bg-white/[0.03] dark:border-gray-800">
                                <div class="grid grid-cols-1 gap-2">
                                    <input type="text" wire:model="new_witness_name" placeholder="Nama saksi..." class="w-full bg-white border-none rounded-lg text-[10px] py-2 px-3 font-bold text-slate-700 shadow-sm dark:bg-white/[0.05] dark:text-gray-200">
                                    <input type="text" wire:model="new_witness_role" placeholder="Jabatan (Apoteker/Keuangan)..." class="w-full bg-white border-none rounded-lg text-[10px] py-2 px-3 font-bold text-slate-700 shadow-sm dark:bg-white/[0.05] dark:text-gray-200">
                                    <button wire:click="addWitness" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2 text-[10px] font-black uppercase tracking-widest transition-all">
                                        TAMBAH SAKSI
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Item List --}}
        <div class="lg:col-span-3 space-y-6">
            @if(!$isViewOnly)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 overflow-hidden relative dark:bg-white/[0.03] dark:border-gray-800">
                    <div class="absolute -right-4 -top-4 opacity-10">
                         <i class="ph-bold ph-magnifying-glass text-8xl text-slate-300"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2 uppercase tracking-tighter dark:text-white">
                        <i class="ph-bold ph-plus-circle text-rose-500"></i> CARI ITEM MANUAL
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10">
                        <div class="relative">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Cari Nama / Kode</label>
                            <div class="relative">
                                <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 dark:text-gray-600"></i>
                                <input type="text" wire:model.live.debounce.300ms="itemSearch" placeholder="Cari nama barang..." class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-xl font-bold text-slate-700 focus:ring-2 focus:ring-rose-500 transition-all text-sm dark:bg-white/[0.05] dark:text-gray-200">
                            </div>

                            @if(!empty($searchResults))
                                <div class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden dark:bg-gray-900 dark:border-gray-800">
                                    @foreach($searchResults as $result)
                                        <button wire:click="selectItem({{ $result->id }})" class="w-full px-4 py-3 text-left hover:bg-slate-50 flex items-center justify-between group transition-all dark:hover:bg-white/[0.05]">
                                            <div>
                                                <p class="text-xs font-black text-slate-700 group-hover:text-rose-600 uppercase tracking-tight dark:text-gray-200">{{ $result->name }}</p>
                                                <p class="text-[9px] text-slate-400 font-mono uppercase dark:text-gray-500">{{ $result->code }}</p>
                                            </div>
                                            <i class="ph-bold ph-plus text-slate-300 group-hover:text-rose-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            @if($selectedItemForBatch)
                                <div class="animate-in fade-in slide-in-from-top-2 duration-300">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest dark:text-gray-500">Pilih Batch (Stok > 0)</label>
                                    <div class="grid grid-cols-1 gap-2">
                                        @forelse($itemBatches as $b)
                                            <button wire:click="addBatchRow({{ $b->id }})" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl p-3 text-left border border-rose-100 transition-all flex items-center justify-between group dark:bg-rose-500/15 dark:hover:bg-rose-500/20 dark:text-rose-400 dark:border-rose-500/20">
                                                <div>
                                                    <span class="text-[10px] font-black uppercase">{{ $b->batch_number }}</span>
                                                    <span class="text-[10px] text-rose-500 font-bold ml-2 dark:text-rose-400">ED: {{ $b->expired_date->format('d/m/y') }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-black">STOK: {{ number_format($b->current_qty) }}</span>
                                                    <i class="ph-bold ph-plus-circle opacity-30 group-hover:opacity-100"></i>
                                                </div>
                                            </button>
                                        @empty
                                            <div class="p-3 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 dark:bg-white/[0.03] dark:border-gray-800">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase italic dark:text-gray-500">Stok habis di gudang ini.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @else
                                <div class="h-full flex items-center justify-center border-2 border-dashed border-slate-100 rounded-2xl p-4 opacity-40 dark:border-gray-800">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic dark:text-gray-500">Pilih barang disamping dulu...</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between dark:border-gray-800">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 uppercase tracking-tighter dark:text-white">
                        <i class="ph-bold ph-list-bullets text-rose-500"></i> DAFTAR BARANG YANG AKAN {{ strtoupper($type === 'disposal' ? 'DIMUSNAHKAN' : 'DIRETUR') }}
                    </h3>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase dark:bg-white/[0.05] dark:text-gray-400">{{ count($rows) }} ITEM</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-white/[0.02]">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-12 dark:text-gray-500">#</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest dark:text-gray-500">Barang & Batch</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center dark:text-gray-500">Batch / ED</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-24 dark:text-gray-500">Stok</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-32 dark:text-gray-500">Qty Out</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right w-32 dark:text-gray-500">Harga (HPP)</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right w-32 dark:text-gray-500">Subtotal</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest dark:text-gray-500">Alasan</th>
                                @if(!$isViewOnly)
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right dark:text-gray-500">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-gray-800">
                            @forelse($rows as $index => $row)
                                <tr class="group hover:bg-rose-50/20 transition-all border-l-4 border-transparent hover:border-rose-500 dark:hover:bg-rose-500/5">
                                    <td class="px-6 py-4">
                                        <span class="text-[10px] font-black text-slate-300 group-hover:text-rose-500 dark:text-gray-600">0{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-black text-slate-700 uppercase leading-tight dark:text-gray-200">{{ $row['item_name'] }}</p>
                                        <p class="text-[9px] font-mono text-slate-400 uppercase dark:text-gray-500">{{ $row['item_code'] }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[10px] font-black text-indigo-700 block dark:text-indigo-400">{{ $row['batch_number'] }}</span>
                                        <span class="text-[9px] font-bold text-rose-500 bg-rose-50 px-1.5 rounded uppercase leading-none dark:bg-rose-500/15 dark:text-rose-400">ED: {{ $row['expiry_date'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-400 text-xs dark:text-gray-500">
                                        {{ number_format($row['available_qty']) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(!$isViewOnly)
                                            <input type="number" step="0.01" wire:model.live="rows.{{ $index }}.qty" class="w-full bg-slate-50 border-none rounded-xl text-xs font-black text-center py-2 focus:ring-2 focus:ring-rose-500 dark:bg-white/[0.05] dark:text-gray-200">
                                        @else
                                            <p class="text-sm font-black text-slate-800 text-center dark:text-white">{{ number_format($row['qty'], 2) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-500">Rp</p>
                                        <p class="text-xs font-bold text-slate-700 dark:text-gray-200">{{ number_format($row['unit_price'], 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-[10px] font-black text-rose-400 dark:text-rose-400">Rp</p>
                                        <p class="text-xs font-black text-rose-700 dark:text-rose-400">{{ number_format($row['total_value'], 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(!$isViewOnly)
                                            <input type="text" wire:model="rows.{{ $index }}.reason" placeholder="..." class="w-full bg-transparent border-none text-[11px] font-medium text-slate-600 focus:ring-0 placeholder:opacity-30 italic dark:text-gray-300">
                                        @else
                                            <p class="text-[11px] text-slate-600 italic dark:text-gray-300">{{ $row['reason'] ?: '-' }}</p>
                                        @endif

                                        @if(!empty($row['source_type']))
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 mt-1 bg-slate-100 text-slate-500 rounded text-[8px] font-black uppercase tracking-tighter dark:bg-white/[0.05] dark:text-gray-400">
                                                <i class="ph-bold ph-link"></i>
                                                {{ $row['source_type'] === 'adjustment' ? 'ADJ' : 'OPNAME' }} #{{ $row['source_id'] }}
                                            </span>
                                        @endif
                                    </td>
                                    @if(!$isViewOnly)
                                        <td class="px-6 py-4 text-right">
                                            <button wire:click="removeRow({{ $index }})" class="p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all dark:text-gray-600 dark:hover:text-rose-400 dark:hover:bg-rose-500/15">
                                                <i class="ph-bold ph-trash text-lg"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3 opacity-20">
                                            <i class="ph-fill ph-warning-circle text-6xl text-slate-300"></i>
                                            <p class="text-xs font-black uppercase tracking-widest italic">Belum ada item yang akan diproses.</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest -mt-2 dark:text-gray-500">Gunakan INTEGRASI AUDIT di samping untuk memuat barang otomatis</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100 italic dark:bg-white/[0.02] dark:border-gray-800">
                    <p class="text-[10px] font-medium text-slate-400 leading-relaxed uppercase tracking-tighter shadow-inner bg-white/50 p-3 rounded-xl border border-slate-200 dark:bg-white/[0.03] dark:border-gray-800 dark:text-gray-500">
                        <i class="ph-bold ph-warning text-amber-500 mr-1"></i> DISCLAIMER: {{ $type === 'disposal' ? 'Proses pemusnahan ini akan memotong stok fisik secara permanen dari sistem. Pastikan Berita Acara sudah ditandatangani oleh saksi yang berwenang.' : 'Proses retur ini akan memotong stok fisik dan harus disertai bukti dokumen retur ke supplier.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
