@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">📦 Laporan Distribusi</h1>
                <p class="text-gray-600 mt-2">Analisis efisiensi & pola distribusi antar gudang</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" action="{{ route('reports.distribution.index') }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gudang Asal</label>
                        <select name="origin_warehouse_id"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <option value="">-- Semua --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $filters['origin_warehouse_id'] == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gudang Tujuan</label>
                        <select name="destination_warehouse_id"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <option value="">-- Semua --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $filters['destination_warehouse_id'] == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <option value="">-- Semua --</option>
                            <option value="requested" {{ $filters['status'] == 'requested' ? 'selected' : '' }}>Requested
                            </option>
                            <option value="sent" {{ $filters['status'] == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="received" {{ $filters['status'] == 'received' ? 'selected' : '' }}>Received
                            </option>
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

                    <div class="md:col-span-5 flex gap-2">
                        <button type="submit"
                            class="px-6 py-2 bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-all font-semibold">
                            Filter
                        </button>
                        <a href="{{ route('reports.distribution.pdf', $filters) }}"
                            class="px-6 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all font-semibold flex items-center gap-2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Total Distribusi</h3>
                    <p class="text-3xl font-black text-gray-900">
                        {{ number_format($analysis['summary']['total_distributions']) }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Total Item</h3>
                    <p class="text-3xl font-black text-gray-900">
                        {{ number_format($analysis['summary']['total_items_moved']) }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Total Nilai</h3>
                    <p class="text-2xl font-black text-gray-900">Rp
                        {{ number_format($analysis['summary']['total_value'], 0, ',', '.') }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Status</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Requested:</span>
                            <span class="font-bold">{{ $analysis['summary']['by_status']['requested'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sent:</span>
                            <span class="font-bold">{{ $analysis['summary']['by_status']['sent'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Received:</span>
                            <span
                                class="font-bold text-green-600">{{ $analysis['summary']['by_status']['received'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Efficiency Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Avg Lead Time</h3>
                    <p class="text-4xl font-black text-blue-600">{{ $analysis['efficiency_metrics']['avg_lead_time'] }}</p>
                    <p class="text-sm text-gray-600 mt-1">days (from sent to received)</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Fill Rate</h3>
                    <p class="text-4xl font-black text-green-600">{{ $analysis['efficiency_metrics']['fill_rate'] }}%</p>
                    <p class="text-sm text-gray-600 mt-1">% requested qty received</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">On-Time Rate</h3>
                    <p class="text-4xl font-black text-purple-600">{{ $analysis['efficiency_metrics']['on_time_rate'] }}%
                    </p>
                    <p class="text-sm text-gray-600 mt-1">received within 3 days</p>
                </div>
            </div>

            <!-- Insights -->
            @if(count($analysis['recommendations']) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-black text-gray-900 mb-4">💡 Smart Insights</h3>
                    <div class="space-y-3">
                        @foreach($analysis['recommendations'] as $insight)
                            <div
                                class="flex items-start gap-3 p-4 bg-{{ $insight['priority'] === 'high' ? 'red' : ($insight['priority'] === 'medium' ? 'amber' : 'blue') }}-50 border border-{{ $insight['priority'] === 'high' ? 'red' : ($insight['priority'] === 'medium' ? 'amber' : 'blue') }}-200 rounded-xl">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-{{ $insight['priority'] === 'high' ? 'red' : ($insight['priority'] === 'medium' ? 'amber' : 'blue') }}-500 rounded-lg flex items-center justify-center text-white">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                </div>
                                <p class="flex-1 font-semibold text-gray-900">{{ $insight['message'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Route Analysis -->
            @if(count($analysis['route_analysis']) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-black text-gray-900 mb-4">🔝 Top Routes</h3>
                    <div class="space-y-3">
                        @foreach(array_slice($analysis['route_analysis'], 0, 5) as $route)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">{{ $route['origin'] }} → {{ $route['destination'] }}</p>
                                    <p class="text-sm text-gray-600">{{ number_format($route['total_items']) }} items moved</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-brand-600">{{ $route['count'] }}x</p>
                                    <p class="text-xs text-gray-500">Avg: {{ $route['avg_lead_time'] }} days</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Distribution Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-black text-gray-900">📋 Distribution History</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">No. Distribusi
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Asal → Tujuan</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Qty</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($distributions as $dist)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $dist->distribution_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $dist->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-gray-900">{{ $dist->origin->name }}</span>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" class="text-gray-400">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg>
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ $dist->destination->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                                        {{ $dist->details->count() }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                                        {{ number_format($dist->details->sum('qty_sent')) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($dist->status === 'received')
                                            <span
                                                class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold">Received</span>
                                        @elseif($dist->status === 'sent')
                                            <span
                                                class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">Sent</span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-bold">Requested</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        Tidak ada data distribusi dalam periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($distributions->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $distributions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection