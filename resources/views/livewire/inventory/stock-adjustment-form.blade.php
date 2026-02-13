<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <i class="ph-bold ph-activity text-8xl text-indigo-600"></i>
        </div>
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
            <div>
                <a href="{{ route('inventory.adjustments.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 mb-2 group transition-all">
                    <i class="ph-bold ph-arrow-left transition-transform group-hover:-translate-x-1"></i> KEMBALI KE DAFTAR
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                    {{ $isEdit ? 'EDIT ADJUSTMENT' : 'BUAT ADJUSTMENT BARU' }}
                </h1>
                <p class="text-slate-500 text-sm font-medium uppercase tracking-widest mt-1">
                    {{ $adjustment_number }} • <span class="{{ $status === 'draft' ? 'text-amber-500' : ($status === 'submitted' ? 'text-indigo-500' : 'text-emerald-500') }}">{{ strtoupper($status) }}</span>
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($status === 'draft' && !$isViewOnly)
                    <button wire:click="saveDraft" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                        <i class="ph-bold ph-floppy-disk"></i> Simpan Draft
                    </button>
                    <button wire:click="submitForReview" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-slate-200 flex items-center gap-2">
                        <i class="ph-bold ph-paper-plane-tilt"></i> Ajukan Review
                    </button>
                @endif

                @if($status === 'submitted')
                    @php
                        $user = auth()->user();
                        $canApproveNode = false;
                        if ($user->hasRole('super-admin') || $user->hasRole('direktur')) {
                            $canApproveNode = true;
                        } elseif ($user->hasRole('kepala-farmasi') && $total_value <= 5000000) {
                            $canApproveNode = true;
                        } elseif ($user->hasRole('kepala-gudang') && $total_value <= 500000) {
                            $canApproveNode = true;
                        }
                    @endphp

                    @if($canApproveNode)
                        <button wire:click="reject" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                            <i class="ph-bold ph-x-circle"></i> Tolak (Draft)
                        </button>
                        <button wire:click="approve" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-200 flex items-center gap-2">
                            <i class="ph-bold ph-check-circle"></i> Setujui & Posting
                        </button>
                    @else
                        <div class="bg-slate-100 text-slate-500 px-6 py-2.5 rounded-xl font-bold text-sm border border-slate-200 flex items-center gap-2">
                            <i class="ph-bold ph-lock"></i> Menunggu Approval (Tier Tinggi)
                        </div>
                    @endif
                @endif

                @if($status === 'posted')
                    <div class="bg-emerald-50 text-emerald-700 px-6 py-2.5 rounded-xl font-bold text-sm border border-emerald-100 flex items-center gap-2">
                        <i class="ph-bold ph-check-square"></i> SUDAH TERBIT (POSTED)
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left: Adjustment Details --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ph-bold ph-info text-indigo-500"></i> INFORMASI DASAR
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Tipe Adjustment</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="$set('type', 'plus')" {{ $isViewOnly ? 'disabled' : '' }} class="py-2.5 rounded-xl text-xs font-black transition-all border {{ $type === 'plus' ? 'bg-emerald-600 text-white border-emerald-600 shadow-lg shadow-emerald-200' : 'bg-slate-50 text-slate-400 border-transparent hover:bg-slate-100' }}">
                                <i class="ph-bold ph-plus-circle mr-1"></i> PLUS
                            </button>
                            <button wire:click="$set('type', 'minus')" {{ $isViewOnly ? 'disabled' : '' }} class="py-2.5 rounded-xl text-xs font-black transition-all border {{ $type === 'minus' ? 'bg-rose-600 text-white border-rose-600 shadow-lg shadow-rose-200' : 'bg-slate-50 text-slate-400 border-transparent hover:bg-slate-100' }}">
                                <i class="ph-bold ph-minus-circle mr-1"></i> MINUS
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Gudang / Depo</label>
                        <select wire:model.live="warehouse_id" {{ $isEdit || $isViewOnly || !empty($items) ? 'disabled' : '' }} class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700">
                            <option value="">Pilih Gudang...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @if(!empty($items) && !$isViewOnly)
                             <p class="text-[9px] text-amber-600 font-bold mt-1 italic">* Gudang dikunci (item sudah ada)</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Tanggal</label>
                        <input type="date" wire:model="adjustment_date" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Kategori Alasan</label>
                        <select wire:model="reason_category" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700">
                            <option value="">Pilih Alasan...</option>
                            <option value="Data Entry Error">Data Entry Error</option>
                            <option value="Found Stock">Found Stock</option>
                            <option value="Damaged Item">Damaged Item</option>
                            <option value="Expired Item">Expired Item</option>
                            <option value="Theft/Loss">Theft/Loss</option>
                            <option value="System Correction">System Correction</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Catatan / Referensi</label>
                        <textarea wire:model="notes" {{ $isViewOnly ? 'disabled' : '' }} rows="3" class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-300 font-medium italic" placeholder="Jelaskan secara singkat..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Evidence Section --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ph-bold ph-paperclip text-amber-500"></i> BUKTI PENDUKUNG
                </h3>
                <div class="space-y-4">
                    @if($total_value > 1000000)
                        <div class="bg-amber-50 border border-amber-100 p-3 rounded-xl mb-4">
                            <p class="text-[10px] text-amber-700 font-black uppercase leading-tight">
                                <i class="ph-bold ph-warning"></i> MANDATORY:
                                Nilai > 1Jt wajib upload dokumen bukti.
                            </p>
                        </div>
                    @endif

                    @if(!$isViewOnly)
                        <div class="relative group">
                            <input type="file" wire:model="evidence_file" class="hidden" id="evidence-input">
                            <label for="evidence-input" class="w-full flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200 rounded-2xl hover:border-indigo-500 hover:bg-slate-50 transition-all cursor-pointer">
                                <i class="ph-bold ph-cloud-arrow-up text-3xl text-slate-300 group-hover:text-indigo-500 mb-2"></i>
                                <span class="text-[10px] font-black text-slate-400 group-hover:text-indigo-600 uppercase">Klik untuk Upload</span>
                            </label>
                        </div>
                        @error('evidence_file') <span class="text-[10px] text-rose-500 font-bold mt-1 block italic">{{ $message }}</span> @enderror
                    @endif

                    @if($evidence_file)
                        <div class="mt-4 p-3 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <i class="ph-bold ph-file-text text-emerald-600"></i>
                                <span class="text-[10px] font-bold text-emerald-700 truncate capitalize">{{ $evidence_file->getClientOriginalName() }}</span>
                            </div>
                            <button wire:click="$set('evidence_file', null)" class="text-emerald-300 hover:text-rose-500 transition-colors">
                                <i class="ph-bold ph-x-circle text-lg"></i>
                            </button>
                        </div>
                    @elseif($existing_evidence)
                        <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-file-text text-indigo-600"></i>
                                <span class="text-[10px] font-bold text-slate-700">Lihat Bukti Terupload</span>
                            </div>
                            <a href="{{ asset('storage/' . $existing_evidence) }}" target="_blank" class="text-indigo-600 hover:text-indigo-700 underline text-[10px] font-black">BUKA ↗</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- High Value BA Section --}}
            @if($total_value > 5000000)
                <div class="bg-indigo-50/50 rounded-3xl border border-indigo-100 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-indigo-800 mb-4 flex items-center gap-2">
                        <i class="ph-bold ph-certificate text-indigo-600"></i> BERITA ACARA HIGH-VALUE
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-indigo-400 uppercase mb-1 tracking-widest">Laporan Investigasi</label>
                            <textarea wire:model="investigation_report" {{ $isViewOnly ? 'disabled' : '' }} rows="4" class="w-full bg-white border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-300 font-medium italic" placeholder="Kenapa selisih besar ini terjadi?"></textarea>
                            @error('investigation_report') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-indigo-400 uppercase mb-1 tracking-widest">Tindakan Koreksif</label>
                            <textarea wire:model="corrective_action" {{ $isViewOnly ? 'disabled' : '' }} rows="3" class="w-full bg-white border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-300 font-medium italic" placeholder="Apa langkah biar gak kejadian lagi?"></textarea>
                            @error('corrective_action') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Item List & Valuation --}}
        <div class="lg:col-span-3 space-y-6">
            @if(!$isViewOnly)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 relative">
                    <div class="absolute -right-4 -top-4 opacity-10">
                         <i class="ph-bold ph-magnifying-glass text-8xl text-slate-300"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2 uppercase tracking-tighter relative z-10">
                        <i class="ph-bold ph-plus-circle text-indigo-500"></i> CARI & TAMBAH BARANG
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10">
                        <div class="relative">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Nama / Kode Barang</label>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama barang..." class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700">
                            
                            @if(!empty($searchResults))
                                <div class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                                    @foreach($searchResults as $result)
                                        <button wire:click="selectItem({{ $result->id }})" class="w-full px-4 py-3 text-left hover:bg-indigo-50 flex items-center justify-between group transition-all">
                                            <div>
                                                <p class="text-xs font-black text-slate-700 group-hover:text-indigo-600 uppercase tracking-tight">{{ $result->name }}</p>
                                                <p class="text-[9px] text-slate-400 font-mono uppercase">{{ $result->code }}</p>
                                            </div>
                                            <i class="ph-bold ph-plus text-slate-300 group-hover:text-indigo-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2 items-end">
                            <div class="flex-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Pilih Batch (Lokasi: {{ $warehouse_id ? 'Aktif' : 'Belum Dipilih' }})</label>
                                <select wire:model="selectedBatchId" class="w-full bg-slate-50 border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700">
                                    <option value="">Pilih Batch...</option>
                                    @foreach($itemBatches as $b)
                                        <option value="{{ $b->id }}">{{ $b->batch_number }} (ED: {{ $b->expired_date->format('d/m/y') }}) - HP: Rp {{ number_format($b->purchase_price) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button wire:click="addItem" class="bg-indigo-600 hover:bg-indigo-700 text-white h-[44px] px-6 rounded-xl font-bold text-sm transition-all flex items-center gap-2 shadow-lg shadow-indigo-100">
                                <i class="ph-bold ph-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/10">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 uppercase tracking-tighter">
                        <i class="ph-bold ph-list-bullets text-indigo-500"></i> RINCIAN PENYESUAIAN STOK
                    </h3>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Nilai Adjustment</p>
                        <h4 class="text-xl font-black text-slate-800 tracking-tight italic">Rp {{ number_format($total_value) }}</h4>
                    </div>
                </div>

                <div class="overflow-x-auto text-left">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Barang & Batch</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Unit Price</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Stok Sistem</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty Baru</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Selisih</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Sub-Total</th>
                                @if(!$isViewOnly)
                                    <th class="px-6 py-4 text-right"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($items as $index => $item)
                                <tr class="group hover:bg-slate-50/30 transition-all border-l-4 border-transparent hover:border-indigo-500">
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-black text-slate-700 uppercase leading-tight">{{ $item['item_name'] }}</p>
                                        <p class="text-[9px] font-mono text-slate-400 uppercase tracking-tighter">B: {{ $item['batch_number'] }} • ED: {{ $item['expired_date'] }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs font-bold text-slate-500">
                                        {{ number_format($item['unit_price']) }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-400 text-xs">
                                        {{ number_format($item['system_qty']) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if(!$isViewOnly)
                                            <input type="number" wire:model.live="items.{{ $index }}.adjusted_qty" wire:change="updateQty({{ $index }})" class="w-16 bg-slate-50 border-none rounded-xl text-xs font-black text-center py-2 focus:ring-2 focus:ring-indigo-500">
                                        @else
                                            <span class="text-sm font-black text-slate-800">{{ number_format($item['adjusted_qty']) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black tracking-tighter
                                            {{ $item['difference'] > 0 ? 'bg-emerald-50 text-emerald-600' : ($item['difference'] < 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-400') }}">
                                            {{ $item['difference'] > 0 ? '+' . $item['difference'] : $item['difference'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-xs font-bold {{ $item['difference'] != 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                            {{ number_format($item['total_value']) }}
                                        </p>
                                    </td>
                                    @if(!$isViewOnly)
                                        <td class="px-6 py-4 text-right">
                                            <button wire:click="removeItem({{ $index }})" class="p-2 text-slate-200 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
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
                                            <p class="text-xs font-black uppercase tracking-widest italic">Belum ada item yang akan diadjust.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="italic">
                             <p class="text-[9px] font-medium text-slate-400 leading-relaxed uppercase tracking-tighter p-3 bg-white/50 rounded-xl border border-slate-200">
                                <i class="ph-bold ph-shield-check text-indigo-500 mr-1"></i> Penyesuaian stok ini akan mempengaruhi kartu stok & buku besar inventaris.<br>Pastikan data fisik sudah divalidasi dengan benar sebelum mensubmit.
                             </p>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Total Nilai Transaksi:</h4>
                            <p class="text-3xl font-black text-slate-800 italic tracking-tighter">Rp {{ number_format($total_value) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
