<div>
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex flex-1 items-center gap-4">
            <div class="relative w-full md:w-80">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5 17.5L13.875 13.875M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. PR..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 transition-all">
            </div>

            <select wire:model.live="status" class="rounded-lg border border-gray-200 bg-white py-2.5 px-4 text-sm outline-none focus:border-brand-500 dark:border-gray-800 dark:bg-gray-900 transition-all">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="closed">Closed</option>
            </select>
        </div>
        
        <a href="{{ route('procurement.requests.create') }}" class="flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-600 shadow-lg shadow-brand-500/20 transition-all uppercase tracking-wider">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Buat PR Baru
        </a>
    </div>

    <div class="overflow-hidden bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm transition-all">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50/50 text-xs uppercase font-black text-gray-500 dark:bg-white/[0.02] dark:text-gray-400 border-b border-gray-100 dark:border-gray-800">
                <tr>
                    <th class="px-6 py-4">Informasi PR</th>
                    <th class="px-6 py-4">Gudang</th>
                    <th class="px-6 py-4 text-center">Periode</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($requests as $request)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white group-hover:text-brand-500 transition-colors uppercase tracking-tight">{{ $request->request_number }}</span>
                                <span class="text-[11px] text-gray-500 italic">{{ $request->request_date->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                </div>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $request->warehouse->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 capitalize">
                                {{ \Carbon\Carbon::create()->month($request->period_month)->translatedFormat('F') }} {{ $request->period_year }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $colors = [
                                    'draft' => 'bg-gray-100 text-gray-700 ring-gray-600/10',
                                    'submitted' => 'bg-blue-100 text-blue-700 ring-blue-600/10',
                                    'approved' => 'bg-green-100 text-green-700 ring-green-600/10',
                                    'rejected' => 'bg-red-100 text-red-700 ring-red-600/10',
                                    'closed' => 'bg-indigo-100 text-indigo-700 ring-indigo-600/10',
                                ];
                                $color = $colors[$request->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-widest {{ $color }} ring-1 ring-inset shadow-sm">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('procurement.requests.edit', $request->id) }}" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.1667 3.33333C14.3855 3.11451 14.6453 2.94095 14.9312 2.82255C15.2171 2.70414 15.5235 2.6432 15.8333 2.6432C16.1432 2.6432 16.4496 2.70414 16.7355 2.82255C17.0214 2.94095 17.2812 3.11451 17.5 3.33333C17.7188 3.55216 17.8924 3.8119 18.0108 4.0978C18.1292 4.3837 18.1901 4.69013 18.1901 5C18.1901 5.30987 18.1292 5.6163 18.0108 5.9022C17.8924 6.1881 17.7188 6.44784 17.5 6.66667L6.66667 17.5L2.5 18.3333L3.33333 14.1667L14.1667 3.33333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                                @if($request->status === 'draft')
                                    <button wire:click="$dispatch('confirm-delete', { id: {{ $request->id }}, action: 'delete-pr' })" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8333 5.83333L15.1111 15.9444C15.0483 16.8234 14.3164 17.5 13.4355 17.5H6.56447C5.68357 17.5 4.9517 16.8234 4.88889 15.9444L4.16667 5.83333M8.33333 9.16667V14.1667M11.6667 9.16667V14.1667M13.3333 5.83333V4.16667C13.3333 3.24619 12.5871 2.5 11.6667 2.5H8.33333C7.41286 2.5 6.66667 4.16667V5.83333M3.33333 5.83333H16.6667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                            <div class="flex flex-col items-center justify-center opacity-40">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-sm">Belum ada data permintaan pembelian.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $requests->links() }}
    </div>

    <script>
        window.addEventListener('confirm-delete', event => {
            const data = Array.isArray(event.detail) ? event.detail[0] : event.detail;
            if (data.action === 'delete-pr') {
                Swal.fire({
                    title: 'Hapus PR?',
                    text: 'Pastikan PR masih dalam status Draft untuk dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.delete(data.id);
                    }
                });
            }
        });
    </script>
</div>
