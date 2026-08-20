@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-brand-600" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                    Laporan Stok (Buku Stok)
                </h1>
                <p class="text-gray-600 mt-2">Analisis pintar dengan rekomendasi otomatis</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" action="{{ route('reports.stock.index') }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Barang</label>
                        <select name="item_id"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ $filters['item_id'] == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gudang</label>
                        <select name="warehouse_id"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $filters['warehouse_id'] == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="flex-1 px-6 py-2 bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-all font-semibold">
                            Filter
                        </button>
                        @if($data)
                            <a href="{{ route('reports.stock.pdf', $filters) }}"
                                class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all"
                                title="Export PDF">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if($data)
                @if ($aiNarrative)
                    <div class="ai-fade-up rounded-2xl bg-gradient-to-br from-indigo-600 via-brand-500 to-violet-600 p-5 relative overflow-hidden mb-6">
                        <div class="absolute inset-0 opacity-40" style="background: radial-gradient(circle at 90% 0%, rgb(255 255 255 / 0.25), transparent 55%);"></div>
                        <div class="relative flex items-start gap-3.5">
                            <div class="ai-glow-badge w-9 h-9 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4" />
                                    <circle cx="12" cy="12" r="4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-black uppercase tracking-widest text-white/60 mb-1">AI Insight</p>
                                <p class="text-sm font-semibold text-white leading-relaxed">{{ $aiNarrative }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Analysis Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- ABC Class -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-500 uppercase">ABC Class</h3>
                            <span
                                class="px-3 py-1 bg-{{ $data['abc_class']['color'] }}-100 text-{{ $data['abc_class']['color'] }}-700 rounded-lg text-xl font-black">
                                {{ $data['abc_class']['class'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">{{ $data['abc_class']['description'] }}</p>
                    </div>

                    <!-- Movement Pattern -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-500 uppercase">Movement</h3>
                            <span
                                class="px-3 py-1 bg-{{ $data['movement_pattern']['color'] }}-100 text-{{ $data['movement_pattern']['color'] }}-700 rounded-lg text-sm font-bold">
                                {{ $data['movement_pattern']['pattern'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">ADU: {{ $data['movement_pattern']['adu'] }}/day</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $data['movement_pattern']['description'] }}</p>
                    </div>

                    <!-- Health Score -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-500 uppercase">Health Score</h3>
                            <span class="text-3xl font-black text-{{ $data['health_score']['color'] }}-600">
                                {{ $data['health_score']['score'] }}%
                            </span>
                        </div>
                        <p class="text-xs font-bold text-{{ $data['health_score']['color'] }}-700">
                            {{ $data['health_score']['status'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $data['health_score']['description'] }}</p>
                    </div>

                    <!-- Current Stock -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Current Stock</h3>
                        <p class="text-3xl font-black text-gray-900">{{ number_format($data['current_stock']) }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $data['item']->unit->name }}</p>
                    </div>
                </div>

                <!-- Recommendations -->
                @if(count($data['recommendations']) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <h3 class="text-lg font-black text-gray-900 mb-4 flex items-center gap-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-brand-500" stroke="currentColor" stroke-width="2">
                                <path d="M2 12h1"></path>
                                <path d="M21 12h1"></path>
                                <path d="M12 2v1"></path>
                                <path d="M12 21v1"></path>
                                <path d="M4.93 4.93l.707.707"></path>
                                <path d="M18.36 18.36l.707.707"></path>
                                <path d="M4.93 19.07l.707-.707"></path>
                                <path d="M18.36 5.64l.707-.707"></path>
                                <path d="m12 17 2-4h-4l2-4"></path>
                            </svg>
                            Rekomendasi Pintar
                        </h3>
                        <div class="space-y-3">
                            @foreach($data['recommendations'] as $rec)
                                <div
                                    class="flex items-start gap-3 p-4 bg-{{ $rec['priority'] === 'high' ? 'red' : ($rec['priority'] === 'medium' ? 'amber' : 'blue') }}-50 border border-{{ $rec['priority'] === 'high' ? 'red' : ($rec['priority'] === 'medium' ? 'amber' : 'blue') }}-200 rounded-xl">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 bg-{{ $rec['priority'] === 'high' ? 'red' : ($rec['priority'] === 'medium' ? 'amber' : 'blue') }}-500 rounded-lg flex items-center justify-center text-white">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5">
                                            @if($rec['icon'] === 'alert-circle')
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            @elseif($rec['icon'] === 'clock')
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            @else
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <polyline points="19 12 12 19 5 12"></polyline>
                                            @endif
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-900">{{ $rec['message'] }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $rec['action'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Stock Card Table (Buku Stok) -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-gray-400" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            Buku Stok - {{ $data['item']->name }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Periode:
                            {{ \Carbon\Carbon::parse($filters['date_from'])->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($filters['date_to'])->format('d M Y') }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Transaksi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Batch</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Masuk</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Keluar</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Saldo</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($stockCards as $card)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $card->transaction_date->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold">
                                                {{ str_replace('_', ' ', $card->transaction_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $card->batch?->batch_number ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-green-600">
                                            {{ $card->qty_in > 0 ? number_format($card->qty_in) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                                            {{ $card->qty_out > 0 ? number_format($card->qty_out) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-black text-gray-900">
                                            {{ number_format($card->last_stock) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $card->notes }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                            Tidak ada transaksi dalam periode ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($stockCards->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $stockCards->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 text-lg font-semibold">Pilih barang dan gudang untuk melihat laporan</p>
                </div>
            @endif
        </div>
    </div>
@endsection