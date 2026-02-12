@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard Utama" />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">
        <!-- Dashboard Card: Total Obat -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Stok Obat</h4>
                    <h2 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totalStock, 0, ',', '.') }}</h2>
                </div>
                <span class="flex items-center text-sm font-medium text-success-500">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 15V5M10 5L14 9M10 5L6 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    12%
                </span>
            </div>
        </div>

        <!-- Dashboard Card: Expired Soon -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Hampir Kadaluwarsa</h4>
                    <h2 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $nearExpiredCount }}</h2>
                </div>
                <span class="text-sm font-medium text-error-500">Kritis</span>
            </div>
        </div>

        <!-- Dashboard Card: Pending Distribution -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3H1v18h22V7l-7-4z"/><path d="M1 7h15V3"/><path d="M7 12h10"/><path d="M7 16h10"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Mutasi Pending</h4>
                    <h2 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">8</h2>
                </div>
                <span class="text-sm font-medium text-orange-500">Menunggu</span>
            </div>
        </div>

        <!-- Dashboard Card: Warehouse Source -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.91A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.79 1.09L21 9"/><path d="M12 3v6"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Gudang Aktif</h4>
                    <h2 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ Auth::user()->warehouse->name ?? 'Pusat' }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 md:gap-6 xl:grid-cols-2">
        <!-- Recent Transaksi Placeholder -->
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="font-semibold text-gray-800 text-theme-lg dark:text-white/90">Transaksi Terakhir</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitoring mutasi barang secara realtime.</p>
            
            <div class="mt-6 overflow-hidden border-t border-gray-100 dark:border-gray-800">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="py-3 font-medium text-gray-700 dark:text-gray-400">Tanggal</th>
                            <th class="py-3 font-medium text-gray-700 dark:text-gray-400">Jenis</th>
                            <th class="py-3 font-medium text-gray-700 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-600 dark:text-gray-300">
                        @forelse($recentActivities as $activity)
                            <tr>
                                <td class="py-3">{{ $activity->transaction_date->format('d M Y') }}</td>
                                <td class="py-3">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-800 dark:text-white/90">{{ ucwords(str_replace('_', ' ', $activity->transaction_type)) }}</span>
                                        <span class="text-xs text-gray-500">{{ $activity->item->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs {{ $activity->qty_in > 0 ? 'bg-success-50 text-success-600' : 'bg-rose-50 text-rose-600' }}">
                                        {{ $activity->qty_in > 0 ? '+' . $activity->qty_in : '-' . $activity->qty_out }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-400">Belum ada transaksi terakhir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Log Placeholder -->
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="font-semibold text-gray-800 text-theme-lg dark:text-white/90">Log Aktivitas</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aktivitas user dalam sistem.</p>
            
            <div class="mt-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="mt-1 h-2 w-2 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Admin Login</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Baru saja • IP: {{ request()->ip() }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="mt-1 h-2 w-2 rounded-full bg-gray-300"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Update Stok Paracetamol</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">2 jam yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
