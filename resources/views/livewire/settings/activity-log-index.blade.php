<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Cari (Nama User / ID Record)</label>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Ketik untuk mencari..." class="block w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
        </div>
        <div>
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Modul</label>
            <select wire:model.live="moduleFilter" class="block w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                <option value="">Semua Modul</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}">{{ strtoupper($mod) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Aksi</label>
            <select wire:model.live="actionFilter" class="block w-full px-4 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                <option value="">Semua Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}">{{ strtoupper($act) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button wire:click="resetFilters" class="w-full px-4 py-2 border border-brand-200 text-brand-600 text-sm font-bold rounded-xl hover:bg-brand-50 transition-all">Reset Filter</button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Waktu</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">User</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-center">Modul / Aksi</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Detail Perubahan</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-gray-500">Metadata</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-900 block truncate">{{ $log->created_at->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-gray-500 font-mono tracking-tighter">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-brand-600 block">{{ $log->user->name ?? 'SYSTEM' }}</span>
                            <span class="text-[10px] text-gray-400">{{ $log->user->username ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-[9px] font-black uppercase tracking-widest text-gray-500 border border-gray-200">
                                    {{ $log->module }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border {{ $log->action == 'delete' ? 'bg-red-50 text-red-600 border-red-100' : ($log->action == 'create' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-blue-50 text-blue-600 border-blue-100') }}">
                                    {{ $log->action }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs overflow-hidden">
                                <span class="text-[10px] font-bold text-gray-700 block mb-1 uppercase tracking-wider">ID Record: {{ $log->record_id ?: '-' }}</span>
                                @if(count($log->changes) > 0)
                                    <div class="flex flex-col gap-1 max-h-24 overflow-y-auto pr-2 custom-scrollbar">
                                        @foreach($log->changes as $key => $change)
                                            <div class="text-[9px] bg-gray-50 p-1 rounded border border-gray-100">
                                                <span class="font-black text-gray-500">{{ strtoupper($key) }}:</span>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-red-500 line-through truncate max-w-[50px]">{{ is_array($change['old']) ? 'object' : $change['old'] }}</span>
                                                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                                                    <span class="text-green-600 font-bold truncate max-w-[50px]">{{ is_array($change['new']) ? 'object' : $change['new'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] text-gray-400 italic font-medium">No detail changes.</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[10px] font-bold text-gray-900 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">{{ $log->ip_address }}</span>
                                <span class="text-[9px] text-gray-400 max-w-[150px] truncate" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                            Tidak ada log aktivitas ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
