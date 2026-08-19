<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-white dark:bg-white/[0.03] rounded-3xl border border-slate-100 dark:border-gray-800 shadow-sm p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <i class="ph-bold ph-arrows-counter-clockwise text-8xl text-indigo-600 dark:text-indigo-400"></i>
        </div>
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
            <div>
                <a href="{{ route('inventory.returns.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 flex items-center gap-1 mb-2 group transition-all">
                    <i class="ph-bold ph-arrow-left transition-transform group-hover:-translate-x-1"></i> KEMBALI KE DAFTAR
                </a>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                    {{ $isEdit ? 'EDIT RETUR' : 'BUAT RETUR BARU' }}
                </h1>
                <p class="text-slate-500 dark:text-gray-400 text-sm font-medium uppercase tracking-widest mt-1">
                    {{ $return_number }} • <span class="{{ $status === 'draft' ? 'text-amber-500 dark:text-amber-400' : ($status === 'submitted' ? 'text-indigo-500 dark:text-indigo-400' : 'text-emerald-500 dark:text-emerald-400') }}">{{ strtoupper(str_replace('_', ' ', $status)) }}</span>
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($status === 'draft' && !$isViewOnly)
                    <button wire:click="saveDraft" wire:loading.attr="disabled" class="bg-slate-100 hover:bg-slate-200 text-slate-700 dark:text-gray-300 px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveDraft"><i class="ph-bold ph-floppy-disk"></i> Simpan Draft</span>
                        <span wire:loading wire:target="saveDraft" class="flex items-center gap-2"><i class="ph-bold ph-circle-notch animate-spin"></i> Menyimpan...</span>
                    </button>
                    <button wire:click="submitForReview" wire:loading.attr="disabled" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-slate-200 flex items-center gap-2">
                        <span wire:loading.remove wire:target="submitForReview"><i class="ph-bold ph-paper-plane-tilt"></i> Ajukan Review</span>
                        <span wire:loading wire:target="submitForReview" class="flex items-center gap-2"><i class="ph-bold ph-circle-notch animate-spin"></i> Memproses...</span>
                    </button>
                @endif

                {{-- Workflow Actions --}}
                @if($status === 'submitted' && auth()->user()->can('returns.approve'))
                    <button wire:click="reject" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-6 py-2.5 rounded-xl font-bold text-sm transition-all">Tolak (Draft)</button>
                    <button wire:click="approve" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-100">Approve & Potong Stok</button>
                @endif

                @if($status === 'approved' && $type === 'supplier')
                    <button wire:click="notifySupplier" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-indigo-100">Notify Supplier</button>
                @endif

                @if($status === 'supplier_notified')
                    <button wire:click="markPickedUp" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-orange-100">Barang Diambil (Picked Up)</button>
                @endif

                @if($status === 'picked_up' || ($status === 'approved' && $type === 'internal'))
                    <button wire:click="complete" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-100">Selesaikan (Complete)</button>
                @endif

                @if($isViewOnly && $status === 'completed')
                    <div class="bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 px-6 py-2.5 rounded-xl font-bold text-sm border border-emerald-100 dark:border-emerald-500/20 flex items-center gap-2">
                        <i class="ph-bold ph-check-square"></i> TRANSFEKSI SELESAI
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left: Order Details --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-white/[0.03] rounded-3xl border border-slate-100 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="ph-bold ph-info text-indigo-500"></i> INFORMASI DASAR
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">Tipe Retur</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="$set('type', 'supplier')" {{ $isEdit || $isViewOnly ? 'disabled' : '' }} class="py-2.5 rounded-xl text-xs font-black transition-all border {{ $type === 'supplier' ? 'bg-rose-600 text-white border-rose-600 shadow-lg shadow-rose-200' : 'bg-slate-50 text-slate-400 dark:text-gray-500 border-transparent hover:bg-slate-100' }}">SUPPLIER</button>
                            <button wire:click="$set('type', 'internal')" {{ $isEdit || $isViewOnly ? 'disabled' : '' }} class="py-2.5 rounded-xl text-xs font-black transition-all border {{ $type === 'internal' ? 'bg-emerald-600 text-white border-emerald-600 shadow-lg shadow-emerald-200' : 'bg-slate-50 text-slate-400 dark:text-gray-500 border-transparent hover:bg-slate-100' }}">INTERNAL</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">Asal Barang (Warehouse)</label>
                        <select wire:model.live="from_warehouse_id" {{ $isEdit || $isViewOnly || !empty($items) ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:text-gray-300 @error('from_warehouse_id') ring-2 ring-rose-500 @enderror">
                            <option value="">Pilih Gudang...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('from_warehouse_id') <span class="text-[10px] text-rose-500 font-bold mt-1">Wajib pilih gudang</span> @enderror
                    </div>

                    @if($type === 'supplier')
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">Pilih Supplier</label>
                            <select wire:model="supplier_id" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:text-gray-300 @error('supplier_id') ring-2 ring-rose-500 @enderror">
                                <option value="">Pilih Supplier...</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="text-[10px] text-rose-500 font-bold mt-1">Wajib pilih supplier</span> @enderror
                        </div>
                    @else
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">Gudang Tujuan</label>
                            <select wire:model="to_warehouse_id" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:text-gray-300 @error('to_warehouse_id') ring-2 ring-rose-500 @enderror">
                                <option value="">Pilih Gudang Tujuan...</option>
                                @foreach($warehouses as $wh)
                                    @if($wh->id != $from_warehouse_id)
                                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('to_warehouse_id') <span class="text-[10px] text-rose-500 font-bold mt-1">Wajib pilih gudang tujuan</span> @enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">Tanggal Retur</label>
                        <input type="date" wire:model="return_date" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:text-gray-300">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">Kategori Alasan</label>
                        <select wire:model="reason_category" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:text-gray-300 @error('reason_category') ring-2 ring-rose-500 @enderror">
                            <option value="">Pilih Alasan...</option>
                            @if($type === 'supplier')
                                <option value="Damaged">Damaged</option>
                                <option value="Wrong Item">Wrong Item</option>
                                <option value="Quality Issue">Quality Issue</option>
                                <option value="Expired">Expired</option>
                                <option value="Overage">Overage</option>
                                <option value="Others">Others</option>
                            @else
                                <option value="Overage Stock">Overage Stock</option>
                                <option value="Wrong Delivery">Wrong Delivery</option>
                                <option value="Near Expired">Near Expired</option>
                                <option value="Consolidation">Consolidation</option>
                            @endif
                        </select>
                        @error('reason_category') <span class="text-[10px] text-rose-500 font-bold mt-1">Wajib pilih alasan retur</span> @enderror
                    </div>
                </div>
            </div>

            @if($type === 'supplier')
                <div class="bg-white dark:bg-white/[0.03] rounded-3xl border border-slate-100 dark:border-gray-800 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 uppercase tracking-tighter">
                        <i class="ph-bold ph-file-text text-indigo-500"></i> REFERENSI DOKUMEN
                    </h3>
                    <div class="space-y-4">
                        <div class="relative group">
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">No. Receiving (Penerimaan)</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="receiving_number" {{ $isViewOnly ? 'disabled' : '' }} class="flex-1 bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 font-bold placeholder:text-slate-300" placeholder="REC/...">
                                @if(!$isViewOnly)
                                    <button wire:click="loadFromReceiving" class="p-3 bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-100 transition-all shadow-sm" title="Tarik data dari Receiving">
                                        <i class="ph-fill ph-magic-wand text-lg"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">No. PO (Purchase Order)</label>
                            <input type="text" wire:model="po_number" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 font-bold placeholder:text-slate-300" placeholder="PO/...">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">No. Invoice Supplier</label>
                            <input type="text" wire:model="invoice_number" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 font-bold placeholder:text-slate-300" placeholder="INV-...">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">No. Surat Jalan (DO) Supplier</label>
                            <input type="text" wire:model="supplier_do_number" {{ $isViewOnly ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 font-bold placeholder:text-slate-300" placeholder="DO-...">
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-white/[0.03] rounded-3xl border border-slate-100 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="ph-bold ph-paperclip text-amber-500"></i> BUKTI FISIK
                </h3>
                @if(!$isViewOnly)
                    <div class="relative group">
                        <input type="file" wire:model="evidence_file" class="hidden" id="evidence-input">
                        <label for="evidence-input" class="w-full flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200 dark:border-gray-800 rounded-2xl hover:border-indigo-500 hover:bg-slate-50 dark:hover:bg-white/[0.03] transition-all cursor-pointer">
                            <i class="ph-bold ph-cloud-arrow-up text-3xl text-slate-300 group-hover:text-indigo-500 mb-2"></i>
                            <span class="text-[10px] font-black text-slate-400 dark:text-gray-500 group-hover:text-indigo-600 uppercase">Klik untuk Upload</span>
                        </label>
                    </div>
                @endif
                @if($evidence_file)
                     <div class="mt-4 p-3 bg-emerald-50 dark:bg-emerald-500/15 rounded-xl border border-emerald-100 dark:border-emerald-500/20 flex items-center justify-between">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <i class="ph-bold ph-file-text text-emerald-600 dark:text-emerald-400"></i>
                            <span class="text-[10px] font-bold text-emerald-700 truncate capitalize">{{ $evidence_file->getClientOriginalName() }}</span>
                        </div>
                        <button wire:click="$set('evidence_file', null)" class="text-emerald-300 hover:text-rose-500">
                            <i class="ph-bold ph-x-circle text-lg"></i>
                        </button>
                    </div>
                @elseif($existing_evidence)
                    <div class="mt-4 p-3 bg-slate-50 dark:bg-white/[0.03] rounded-xl border border-slate-100 dark:border-gray-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="ph-bold ph-file-text text-indigo-600 dark:text-indigo-400"></i>
                            <span class="text-[10px] font-bold text-slate-700 dark:text-gray-300 uppercase">Bukti Terupload</span>
                        </div>
                        <a href="{{ asset('storage/' . $existing_evidence) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 underline text-[10px] font-black">LIHAT ↗</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Item List & Valuation --}}
        <div class="lg:col-span-3 space-y-6">
            @if(!$isViewOnly)
                <div class="bg-white dark:bg-white/[0.03] rounded-3xl border border-slate-100 dark:border-gray-800 shadow-sm p-6 relative">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-tighter">
                            <i class="ph-bold ph-magnifying-glass text-indigo-500"></i> CARI ITEM DARI STOK ({{ $from_warehouse_id ? 'AKTIF' : 'BELUM PILIH GUDANG' }})
                        </h3>
                        
                        {{-- Smart Load Buttons --}}
                        <div class="flex gap-2">
                             <button type="button" wire:click="loadExpiredItems" class="px-3 py-1.5 bg-rose-50 text-rose-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-rose-100 hover:bg-rose-600 hover:text-white transition-all flex items-center gap-1.5">
                                <i class="ph-bold ph-calendar-x"></i> EXPIRED
                            </button>
                            <button type="button" wire:click="loadDamagedFromAdjustments" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-amber-100 dark:border-amber-500/20 hover:bg-amber-600 hover:text-white transition-all flex items-center gap-1.5">
                                <i class="ph-bold ph-wrench"></i> ADJUSTMENT
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest">Nama / Kode Barang</label>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama barang..." class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:text-gray-300">
                            
                            @if(!empty($searchResults))
                                <div class="absolute z-50 w-full mt-2 bg-white dark:bg-white/[0.03] rounded-2xl shadow-2xl border border-slate-100 dark:border-gray-800 overflow-hidden text-left">
                                    @foreach($searchResults as $result)
                                        <button wire:click="selectItem({{ $result->id }})" class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-white/[0.03] flex items-center justify-between group transition-all">
                                            <div>
                                                <p class="text-xs font-black text-slate-700 dark:text-gray-300 group-hover:text-indigo-600 uppercase tracking-tight">{{ $result->name }}</p>
                                                <p class="text-[9px] text-slate-400 dark:text-gray-500 font-mono uppercase tracking-tighter">{{ $result->code }}</p>
                                            </div>
                                            <i class="ph-bold ph-plus text-slate-300 group-hover:text-indigo-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2 items-end">
                            <div class="flex-1 text-left">
                                <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-1 tracking-widest text-left">Pilih Batch & ED</label>
                                <select wire:model="selectedBatchId" class="w-full bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-sm py-3 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 dark:text-gray-300">
                                    <option value="">Pilih Batch...</option>
                                    @foreach($itemBatches as $b)
                                        <option value="{{ $b->id }}">{{ $b->batch_number }} (ED: {{ $b->expired_date->format('d/m/y') }}) - Stok: {{ $b->current_qty }}</option>
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

            <div class="bg-white dark:bg-white/[0.03] rounded-3xl border border-slate-100 dark:border-gray-800 shadow-sm p-8 min-h-[400px] flex flex-col relative overflow-hidden @error('items') border-rose-300 @enderror">
                <div class="flex justify-between items-center mb-8 relative z-10">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2 tracking-tighter uppercase">
                        <i class="ph-bold ph-list-bullets text-indigo-500"></i> DAFTAR BARANG YANG DIRETUR
                    </h3>
                    @error('items') <span class="text-xs text-rose-500 font-bold bg-rose-50 px-3 py-1 rounded-lg">Minimal harus ada 1 barang</span> @enderror
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest leading-none mb-1 text-right">Total Nilai Retur</p>
                        <h4 class="text-xl font-black text-slate-800 dark:text-white tracking-tight italic text-right">Rp {{ number_format($total_value) }}</h4>
                    </div>
                </div>

                <div class="overflow-x-auto text-left">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-white/[0.02]">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest text-left">Item & Batch</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest text-right">Harga Retur</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest text-center">Batch Stok</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest text-center">Qty Retur</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest text-right">Sub-Total</th>
                                @if(!$isViewOnly)
                                    <th class="px-6 py-4 text-right"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-gray-800">
                            @forelse($items as $index => $item)
                                <tr class="group hover:bg-slate-50/30 transition-all border-l-4 border-transparent hover:border-indigo-500">
                                    <td class="px-6 py-4 text-left">
                                        <p class="text-xs font-black text-slate-700 dark:text-gray-300 uppercase leading-tight">{{ $item['item_name'] }}</p>
                                        <div class="flex items-center gap-2">
                                            <p class="text-[9px] font-mono text-slate-400 dark:text-gray-500 uppercase tracking-tighter">B: {{ $item['batch_number'] }} • ED: {{ $item['expired_date'] }}</p>
                                            @if(!empty($item['source_type']))
                                                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 dark:text-gray-400 rounded text-[7px] font-black uppercase tracking-tighter flex items-center gap-1">
                                                    <i class="ph-bold ph-link"></i> {{ $item['source_type'] === 'adjustment' ? 'ADJ' : 'OPNAME' }} #{{ $item['source_id'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-gray-400">
                                        {{ number_format($item['price']) }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-400 dark:text-gray-500 text-xs">
                                        {{ number_format($item['available_qty']) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if(!$isViewOnly)
                                            <div class="flex items-center justify-center gap-2">
                                                <input type="number" wire:model.live="items.{{ $index }}.qty" wire:change="updateQty({{ $index }})" class="w-16 bg-slate-50 dark:bg-white/[0.03] border-none rounded-xl text-xs font-black text-center py-2 focus:ring-2 focus:ring-indigo-500">
                                            </div>
                                        @else
                                            <span class="text-sm font-black text-slate-800 dark:text-white">{{ number_format($item['qty']) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-xs font-black text-slate-800 dark:text-white">
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
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3 opacity-20">
                                            <i class="ph-fill ph-warning-circle text-6xl text-slate-300"></i>
                                            <p class="text-xs font-black uppercase tracking-widest italic">Belum ada barang dipilih.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 dark:bg-white/[0.03] border-t border-slate-100 dark:border-gray-800">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-left">
                        <div class="text-left w-full md:w-2/3">
                            <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase mb-2 tracking-widest leading-none flex items-center gap-2">
                                Penjelasan Detail / Catatan Investigasi
                                @error('reason') <span class="bg-rose-500 text-white px-2 py-0.5 rounded text-[8px] animate-pulse">Wajib Diisi</span> @enderror
                            </label>
                            <textarea wire:model="reason" {{ $isViewOnly ? 'disabled' : '' }} rows="3" class="w-full bg-white dark:bg-white/[0.03] border-2 border-transparent {{ $errors->has('reason') ? 'border-rose-300' : '' }} rounded-2xl text-sm py-4 focus:ring-2 focus:ring-indigo-500 font-medium italic placeholder:text-slate-200 transition-all" placeholder="Jelaskan alasan retur secara rinci..."></textarea>
                        </div>
                        <div class="text-right w-full md:w-1/3 flex flex-col items-end">
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest mb-1 italic text-right">Estimasi Nilai Kredit:</h4>
                            <p class="text-3xl font-black text-slate-800 dark:text-white italic tracking-tighter text-right">Rp {{ number_format($total_value) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Credit Note Tracking Section (Supplier Only & Post-Approval) --}}
            @if($type === 'supplier' && in_array($status, ['approved', 'supplier_notified', 'picked_up', 'completed']))
                <div class="bg-indigo-900 rounded-3xl border border-indigo-800 shadow-xl p-6 text-white relative overflow-hidden">
                    <div class="absolute -top-4 -right-4 opacity-10">
                        <i class="ph-fill ph-bank text-9xl"></i>
                    </div>
                    
                    <div class="relative z-10 text-left">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black uppercase tracking-widest flex items-center gap-2">
                                <i class="ph-bold ph-credit-card text-amber-400"></i> TRACKING CREDIT NOTE
                            </h3>
                            @if(auth()->user()->can('returns.input-credit-note') && !$showCreditNoteForm)
                                <button wire:click="$set('showCreditNoteForm', true)" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                                    <i class="ph-bold ph-plus"></i> INPUT BARU
                                </button>
                            @endif
                        </div>

                        {{-- CN Input Form --}}
                        @if($showCreditNoteForm)
                            <div class="bg-white/10 rounded-2xl p-4 mb-6 border border-white/5 space-y-4 text-left">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2 text-left">
                                        <label class="block text-[9px] font-black text-indigo-200 uppercase mb-1">Nomor Credit Note</label>
                                        <input type="text" wire:model="cn_number" class="w-full bg-white/10 border-none rounded-xl text-xs py-2.5 text-white placeholder:text-indigo-300/50 focus:ring-1 focus:ring-amber-400 font-bold">
                                    </div>
                                    <div class="text-left">
                                        <label class="block text-[9px] font-black text-indigo-200 uppercase mb-1">Nominal (Rp)</label>
                                        <input type="number" wire:model="cn_amount" class="w-full bg-white/10 border-none rounded-xl text-xs py-2.5 text-white placeholder:text-indigo-300/50 focus:ring-1 focus:ring-amber-400 font-bold">
                                    </div>
                                    <div class="text-left">
                                        <label class="block text-[9px] font-black text-indigo-200 uppercase mb-1">Tanggal Nota</label>
                                        <input type="date" wire:model="cn_date" class="w-full bg-white/10 border-none rounded-xl text-xs py-2.5 text-white focus:ring-1 focus:ring-amber-400 font-bold">
                                    </div>
                                    <div class="text-left">
                                        <label class="block text-[9px] font-black text-indigo-200 uppercase mb-1">Tipe Kredit</label>
                                        <select wire:model="cn_type" class="w-full bg-white/10 border-none rounded-xl text-xs py-2.5 text-white focus:ring-1 focus:ring-amber-400 font-bold">
                                            <option value="credit_memo" class="bg-indigo-900">Credit Memo</option>
                                            <option value="refund" class="bg-indigo-900">Refund (Cash)</option>
                                            <option value="replacement" class="bg-indigo-900">Replacement</option>
                                        </select>
                                    </div>
                                    <div class="text-left">
                                        <label class="block text-[9px] font-black text-indigo-200 uppercase mb-1">Status</label>
                                        <select wire:model="cn_status" class="w-full bg-white/10 border-none rounded-xl text-xs py-2.5 text-white focus:ring-1 focus:ring-amber-400 font-bold">
                                            <option value="pending" class="bg-indigo-900">Pending</option>
                                            <option value="received" class="bg-indigo-900">Received</option>
                                            <option value="applied" class="bg-indigo-900">Applied</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <button wire:click="resetCreditNoteForm" class="flex-1 py-2 text-xs font-black text-white/50 hover:text-white transition-all">BATAL</button>
                                    <button wire:click="saveCreditNote" class="flex-1 py-2 bg-amber-400 text-indigo-900 rounded-xl text-xs font-black hover:bg-amber-300 transition-all shadow-lg shadow-amber-400/20">SIMPAN NOTA</button>
                                </div>
                            </div>
                        @endif

                        {{-- CN List --}}
                        <div class="space-y-3">
                            @forelse($credit_notes as $cn)
                                <div class="bg-white/5 border border-white/5 rounded-2xl p-4 group transition-all hover:bg-white/10 text-left">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="text-left">
                                            <p class="text-[10px] font-black text-amber-400 leading-none mb-1">{{ strtoupper(str_replace('_', ' ', $cn['type'])) }}</p>
                                            <h4 class="text-sm font-black text-white tracking-tight">{{ $cn['credit_note_number'] }}</h4>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter 
                                            {{ $cn['status'] === 'applied' ? 'bg-emerald-500/20 text-emerald-400' : ($cn['status'] === 'received' ? 'bg-blue-500/20 text-blue-400' : 'bg-amber-500/20 text-amber-400') }}">
                                            {{ $cn['status'] }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-end">
                                        <div class="text-left">
                                            <p class="text-[10px] text-indigo-300/50 font-bold uppercase">{{ \Carbon\Carbon::parse($cn['note_date'])->format('d M Y') }}</p>
                                            <p class="text-lg font-black text-white italic tracking-tighter leading-none mt-1 text-left">Rp {{ number_format($cn['amount']) }}</p>
                                        </div>
                                        @if(auth()->user()->can('returns.input-credit-note'))
                                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-all">
                                                <button wire:click="deleteCreditNote({{ $cn['id'] }})" class="p-1.5 text-white/20 hover:text-rose-400">
                                                    <i class="ph ph-trash text-lg"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 opacity-20">
                                    <i class="ph ph-mask-sad text-4xl mb-2"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Belum ada Credit Note</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
