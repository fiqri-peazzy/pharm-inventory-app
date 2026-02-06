<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stock Value -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-blue-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-blue-100">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Nilai Stok</h4>
                <p class="text-2xl font-black text-gray-900 mt-1">Rp{{ number_format($summary['total_value']) }}</p>
                <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-full mt-2 inline-block">Estimasi HNA + PPN</span>
            </div>
        </div>

        <!-- Near Expired -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-red-100">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Akan Kadaluarsa</h4>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($summary['near_expired_count']) }} <span class="text-sm font-medium text-gray-400">Items</span></p>
                <span class="text-[10px] text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded-full mt-2 inline-block">Dalam 6 Bulan Ke Depan</span>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-amber-100">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Stok Kritis</h4>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($summary['low_stock_count']) }} <span class="text-sm font-medium text-gray-400">Items</span></p>
                <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded-full mt-2 inline-block">Dibawah Batas Minimum</span>
            </div>
        </div>

        <!-- Total Items -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-brand-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-brand-100">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                </div>
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Katalog Item</h4>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($summary['total_items']) }} <span class="text-sm font-medium text-gray-400">Master</span></p>
                <span class="text-[10px] text-brand-600 font-bold bg-brand-50 px-2 py-0.5 rounded-full mt-2 inline-block">Aktif di Semua Gudang</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Near Expired Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-50 text-red-500 rounded-xl">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-800">Alert: Barang Mendekati Kadaluarsa</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider">Nama Item</th>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider text-center">Batch</th>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider text-center">Qty Sisa</th>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider">Exp Date</th>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($nearExpiredItems as $batch)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900 block">{{ $batch->item->name }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ $batch->warehouse->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-gray-600">{{ $batch->batch_number }}</td>
                                    <td class="px-6 py-4 text-center font-black text-brand-600">{{ number_format($batch->current_qty) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900 block">{{ $batch->expired_date->format('d/m/Y') }}</span>
                                        @php $days = \Carbon\Carbon::now()->diffInDays($batch->expired_date, false); @endphp
                                        <span class="text-[9px] font-black uppercase {{ $days < 30 ? 'text-red-500' : 'text-amber-500' }}">
                                            {{ $days < 0 ? 'KADALUARSA' : $days . ' Hari Lagi' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="px-3 py-1 bg-gray-50 text-gray-400 text-[10px] font-black uppercase rounded-lg border border-gray-100 opacity-50 cursor-not-allowed">Proses</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Tidak ada barang mendekati kadaluarsa dalam 6 bulan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-50 text-amber-500 rounded-xl">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-800">Alert: Stok Kritis (Dibawah Minimum)</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider">Nama Item</th>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider text-center">Stok Saat Ini</th>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider text-center">Min. Stock</th>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-wider text-right">Kebutuhan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($lowStockItems as $item)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900 block">{{ $item->name }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ $item->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-red-500">{{ number_format($item->current_total_stock) }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-400">{{ number_format($item->min_stock) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('procurement.requests.create') }}" class="px-3 py-1 bg-brand-50 text-brand-600 text-[10px] font-black uppercase rounded-lg border border-brand-100 hover:bg-brand-500 hover:text-white transition-all">Order Lagi</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Semua stok masih dalam kondisi aman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Components -->
        <div class="space-y-6">
            <!-- Warehouse Breakdown -->
            <div class="bg-gray-900 rounded-3xl p-6 shadow-xl shadow-gray-200">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Sebaran Stok Gudang</h3>
                <div class="space-y-6">
                    @foreach($warehouseStock as $wh)
                        <div class="space-y-2">
                            <div class="flex justify-between items-end">
                                <span class="text-sm font-bold text-white">{{ $wh['name'] }}</span>
                                <span class="text-[10px] text-gray-400 font-black uppercase">Rp{{ number_format($wh['total_value'] / 1000000, 1) }}JT</span>
                            </div>
                            <div class="h-2 w-full bg-gray-800 rounded-full overflow-hidden flex">
                                @php $percent = $summary['total_value'] > 0 ? ($wh['total_value'] / $summary['total_value']) * 100 : 0; @endphp
                                <div class="bg-brand-500 h-full rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                <span class="text-brand-400">{{ number_format($wh['total_qty']) }} Items</span>
                                <span class="text-gray-500">{{ number_format($percent, 1) }}% Kapasitas</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Penerimaan Terakhir</h3>
                <div class="space-y-6 relative before:absolute before:inset-0 before:left-[15px] before:w-px before:bg-gray-100 before:pointer-events-none">
                    @forelse($recentReceivings as $rcv)
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-1 w-8 h-8 -ml-4 bg-white border-2 border-brand-500 text-brand-500 rounded-xl flex items-center justify-center z-10 shadow-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ $rcv->receiving_date->format('d M Y') }}</p>
                            <h4 class="text-xs font-bold text-gray-900 leading-tight">{{ $rcv->supplier->name }}</h4>
                            <p class="text-[10px] text-gray-500 mt-1">Rp{{ number_format($rcv->grand_total / 1000000, 1) }} Juta via {{ $rcv->warehouse->name }}</p>
                        </div>
                    @empty
                        <p class="text-center text-xs text-gray-400 py-4 italic">Belum ada aktivitas baru.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
