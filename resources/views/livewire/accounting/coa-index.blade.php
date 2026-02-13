<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-1 items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode atau Nama Akun..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
            </div>
            
            <select wire:model.live="typeFilter" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                <option value="">Semua Tipe</option>
                <option value="asset">Asset (Aktiva)</option>
                <option value="liability">Liability (Kewajiban)</option>
                <option value="equity">Equity (Modal)</option>
                <option value="revenue">Revenue (Pendapatan)</option>
                <option value="expense">Expense (Biaya)</option>
            </select>
        </div>

        <button wire:click="create" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-500 text-white text-sm font-semibold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Akun
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Kode</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Nama Akun</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-center">Tipe</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-center">D/K</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-center">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($accounts as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors {{ !$item->is_active ? 'opacity-60 grayscale-[0.5]' : '' }}">
                        <td class="px-6 py-4 font-mono text-sm font-bold text-brand-600 tracking-tighter">
                            {{ $item->code }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900 block">{{ $item->name }}</span>
                            @if($item->parent)
                                <span class="text-[10px] text-gray-400 font-medium italic">Parent: {{ $item->parent->name }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $item->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-black text-xs {{ $item->normal_balance == 'debit' ? 'text-blue-500' : 'text-amber-500' }}">
                            {{ strtoupper(substr($item->normal_balance, 0, 1)) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleStatus({{ $item->id }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $item->is_active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $item->id }})" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                            Tidak ada data akun ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $accounts->links() }}

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-white p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">{{ $isEdit ? 'Edit Akun' : 'Tambah Akun Baru' }}</h3>
                            <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 transition-all">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Kode Akun</label>
                                    <input type="text" wire:model="code" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                    @error('code') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Tipe Akun</label>
                                    <select wire:model="type" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all uppercase">
                                        <option value="">-- Pilih --</option>
                                        <option value="asset">Asset (Aktiva)</option>
                                        <option value="liability">Liability (Kewajiban)</option>
                                        <option value="equity">Equity (Modal)</option>
                                        <option value="revenue">Revenue (Pendapatan)</option>
                                        <option value="expense">Expense (Biaya)</option>
                                    </select>
                                    @error('type') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Nama Akun</label>
                                <input type="text" wire:model="name" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                @error('name') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Normal Balance (D/K)</label>
                                <div class="flex gap-4">
                                    <label class="flex-1 flex items-center justify-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all {{ $normal_balance == 'debit' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-100 bg-gray-50 text-gray-400' }}">
                                        <input type="radio" wire:model="normal_balance" value="debit" class="hidden">
                                        <span class="text-xs font-black uppercase">DEBIT</span>
                                    </label>
                                    <label class="flex-1 flex items-center justify-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all {{ $normal_balance == 'credit' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-gray-100 bg-gray-50 text-gray-400' }}">
                                        <input type="radio" wire:model="normal_balance" value="credit" class="hidden">
                                        <span class="text-xs font-black uppercase">KREDIT</span>
                                    </label>
                                </div>
                                @error('normal_balance') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Parent Account (Opsional)</label>
                                <select wire:model="parent_id" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                    <option value="">-- Tanpa Parent --</option>
                                    @foreach($parentAccounts as $p)
                                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Keterangan</label>
                                <textarea wire:model="description" rows="2" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"></textarea>
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                <input type="checkbox" wire:model="is_active" id="acc_is_active" class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 transition-all">
                                <label for="acc_is_active" class="text-sm font-bold text-gray-700">Akun ini aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button wire:click="save" class="px-6 py-2 bg-brand-500 text-white text-sm font-bold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all">
                            {{ $isEdit ? 'Update Akun' : 'Simpan Akun' }}
                        </button>
                        <button wire:click="$set('showModal', false)" class="px-6 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
