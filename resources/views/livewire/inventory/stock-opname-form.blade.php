<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                @if($status === 'draft') Target Stock Opname @elseif($status === 'submitted') Review Stock Opname @else Detail Stock Opname @endif
            </h2>
            <p class="text-slate-500 text-sm">Nomor: <span class="font-mono font-bold text-indigo-600">{{ $opname_number }}</span> | Status: 
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border 
                    @if($status === 'draft') bg-amber-50 text-amber-600 border-amber-100 
                    @elseif($status === 'submitted') bg-indigo-50 text-indigo-600 border-indigo-100 
                    @else bg-emerald-50 text-emerald-600 border-emerald-100 @endif">
                    {{ $status }}
                </span>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('inventory.stock-opnames.index') }}" class="px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-all flex items-center gap-2">
                <i class="ph ph-arrow-left"></i> Kembali
            </a>
            
            @if(!$isViewOnly)
                @if($status === 'draft')
                    <button wire:click="save" class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl transition-all shadow-lg shadow-slate-100 font-bold text-sm">
                        <i class="ph ph-floppy-disk"></i> Simpan Draft
                    </button>
                    <button wire:click="submitForReview" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-xl transition-all shadow-lg shadow-indigo-100 font-bold text-sm">
                        <i class="ph ph-paper-plane-tilt"></i> Ajukan Review
                    </button>
                @elseif($status === 'submitted' && auth()->user()->can('stock-opnames.approve'))
                    <button wire:click="approve" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-xl transition-all shadow-lg shadow-emerald-100 font-bold text-sm">
                        <i class="ph ph-check-circle"></i> Setujui & Update Stok
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Header Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 border-b border-slate-50 pb-3">
                    <i class="ph ph-info text-indigo-500"></i>
                    Informasi Dasar
                </h3>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lokasi Gudang/Depo</label>
                    <select wire:model.live="warehouse_id" class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" {{ ($isEdit || $isViewOnly) ? 'disabled' : '' }}>
                        <option value="">Pilih Gudang...</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Opname</label>
                    <input type="date" wire:model="opname_date" class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" {{ ($status !== 'draft' || $isViewOnly) ? 'disabled' : '' }}>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipe Opname</label>
                    <select wire:model="type" class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" {{ ($status !== 'draft' || $isViewOnly) ? 'disabled' : '' }}>
                        <option value="full">Full Opname (Semua)</option>
                        <option value="partial">Partial (Tertentu)</option>
                        <option value="cycle">Cycle Count</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PIC Hitung</label>
                    <select wire:model="pic_id" class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" {{ ($status !== 'draft' || $isViewOnly) ? 'disabled' : '' }}>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                @unless($isEdit || $isViewOnly)
                    <button wire:click="loadItems" class="w-full py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                        <i class="ph ph-arrows-clockwise"></i>
                        Generate Daftar Item
                    </button>
                @endunless
            </div>

            <div class="bg-indigo-900 p-6 rounded-3xl text-white shadow-lg shadow-indigo-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                        <i class="ph ph-shield-check text-xl text-indigo-200"></i>
                    </div>
                    <h4 class="font-bold">Panduan Opname</h4>
                </div>
                <ul class="text-[11px] space-y-2 text-indigo-100">
                    <li>1. Pilih Gudang yang akan dihitung.</li>
                    <li>2. Klik "Generate" untuk menarik stok sistem saat ini.</li>
                    <li>3. Isi kolom "Fisik" sesuai hitungan di lapangan.</li>
                    <li>4. Pastikan semua selisih memiliki catatan alasan.</li>
                </ul>
            </div>
        </div>

        <!-- Items Table -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden min-h-[500px]">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Daftar Item Perhitungan</h3>
                    <div class="text-xs text-slate-400">Total: <span class="font-bold text-indigo-600">{{ count($rows) }}</span> Item</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Obat / Item</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Batch / ED</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Stok Sistem</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-800 uppercase tracking-wider text-center w-32">Stok Fisik</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Selisih</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($rows as $index => $row)
                                <tr wire:key="row-{{ $index }}" class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-700">{{ $row['item_name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-slate-100 rounded text-[10px] font-mono text-slate-600">{{ $row['batch_number'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-500">{{ $row['system_qty'] }}</td>
                                    <td class="px-6 py-4">
                                        <input type="number" step="0.01" wire:model.blur="rows.{{ $index }}.physical_qty" wire:change="updatePhysical({{ $index }})" 
                                            class="w-full px-3 py-2 bg-slate-100 border-none rounded-lg text-sm text-center font-bold text-indigo-600 focus:ring-2 focus:ring-indigo-500/20"
                                            {{ ($status !== 'draft' || $isViewOnly) ? 'disabled' : '' }}>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($row['difference'] !== null)
                                            <span class="font-bold {{ $row['difference'] < 0 ? 'text-rose-600' : ($row['difference'] > 0 ? 'text-emerald-600' : 'text-slate-400') }}">
                                                {{ $row['difference'] > 0 ? '+' : '' }}{{ $row['difference'] }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" wire:model="rows.{{ $index }}.notes" placeholder="..." 
                                            class="w-full px-3 py-2 bg-slate-50 border-none rounded-lg text-[11px] focus:ring-2 focus:ring-indigo-500/10 transition-all"
                                            {{ ($status !== 'draft' || $isViewOnly) ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="ph ph-package text-4xl text-slate-200"></i>
                                            <p class="text-slate-400 italic">Klik tombol "Generate Daftar Item" untuk memulai perhitungan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
