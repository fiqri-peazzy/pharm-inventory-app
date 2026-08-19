<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-5 gap-4 dark:bg-white/[0.03] dark:border-gray-800">
        <div class="md:col-span-2">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Cari (Nama User / ID Record)</label>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Ketik untuk mencari..." class="block w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white dark:focus:bg-white/[0.05]">
        </div>
        <div>
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Modul</label>
            <select wire:model.live="moduleFilter" class="block w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white dark:focus:bg-white/[0.05]">
                <option value="">Semua Modul</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}">{{ strtoupper($mod) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Aksi</label>
            <select wire:model.live="actionFilter" class="block w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800 dark:text-white dark:focus:bg-white/[0.05]">
                <option value="">Semua Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}">{{ strtoupper($act) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button wire:click="resetFilters" class="w-full px-4 py-2 border border-brand-200 text-brand-600 text-sm font-bold rounded-xl hover:bg-brand-50 transition-all dark:border-brand-500/30 dark:text-brand-400 dark:hover:bg-brand-500/10">Reset Filter</button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 dark:bg-white/[0.02] dark:border-gray-800">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Waktu</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">User</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-center dark:text-gray-400">Modul / Aksi</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Detail Perubahan</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Metadata</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors dark:hover:bg-white/[0.03]">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-900 block truncate dark:text-white">{{ $log->created_at->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-gray-500 font-mono tracking-tighter dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-brand-600 block dark:text-brand-400">{{ $log->user->name ?? 'SYSTEM' }}</span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $log->user->username ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-[9px] font-black uppercase tracking-widest text-gray-500 border border-gray-200 dark:bg-white/[0.05] dark:text-gray-400 dark:border-gray-800">
                                    {{ $log->module }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border {{ $log->action == 'delete' ? 'bg-red-50 text-red-600 border-red-100 dark:bg-red-500/15 dark:text-red-400 dark:border-red-500/20' : ($log->action == 'create' ? 'bg-green-50 text-green-600 border-green-100 dark:bg-green-500/15 dark:text-green-400 dark:border-green-500/20' : 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-500/15 dark:text-blue-400 dark:border-blue-500/20') }}">
                                    {{ $log->action }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs overflow-hidden">
                                <span class="text-[10px] font-bold text-gray-700 block mb-1 uppercase tracking-wider dark:text-gray-300">ID Record: {{ $log->record_id ?: '-' }}</span>
                                @if(count($log->changes) > 0)
                                    <div class="flex flex-col gap-1 max-h-24 overflow-y-auto pr-2 custom-scrollbar">
                                        @foreach($log->changes as $key => $change)
                                            <div class="text-[9px] bg-gray-50 p-1 rounded border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800">
                                                <span class="font-black text-gray-500 dark:text-gray-400">{{ strtoupper($key) }}:</span>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-red-500 line-through truncate max-w-[50px] dark:text-red-400">{{ is_array($change['old']) ? 'object' : $change['old'] }}</span>
                                                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                                                    <span class="text-green-600 font-bold truncate max-w-[50px] dark:text-green-400">{{ is_array($change['new']) ? 'object' : $change['new'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] text-gray-400 italic font-medium dark:text-gray-500">No detail changes.</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[10px] font-bold text-gray-900 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 dark:text-white dark:bg-white/[0.03] dark:border-gray-800">{{ $log->ip_address }}</span>
                                <span class="text-[9px] text-gray-400 max-w-[150px] truncate dark:text-gray-500" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm dark:text-gray-500">
                            Tidak ada log aktivitas ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
