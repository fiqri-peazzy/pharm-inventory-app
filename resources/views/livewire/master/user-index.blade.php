<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-1 items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama, Username, atau Email..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
            </div>
        </div>

        <button wire:click="create" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-500 text-white text-sm font-semibold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah User
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Nama / Username</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Email / Kontak</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Role / Akses</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500">Warehouse/Gudang</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-500 text-center">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors {{ !$item->is_active ? 'opacity-60 grayscale-[0.5]' : '' }}">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900 block">{{ $item->name }}</span>
                            <span class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $item->username }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 block">{{ $item->email }}</span>
                            <span class="text-[10px] text-gray-400">{{ $item->phone ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($item->roles as $role)
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-600 border border-gray-200">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-brand-600">{{ $item->warehouse->name ?? 'Akses Global' }}</span>
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
                            Tidak ada data user ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                    <div class="bg-white p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">{{ $isEdit ? 'Edit User' : 'Tambah User Baru' }}</h3>
                            <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 transition-all">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Nama Lengkap</label>
                                <input type="text" wire:model="name" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                @error('name') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Username</label>
                                <input type="text" wire:model="username" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                @error('username') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Email</label>
                                <input type="email" wire:model="email" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                @error('email') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">No. HP / Kontak</label>
                                <input type="text" wire:model="phone" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Employee ID (NIP)</label>
                                <input type="text" wire:model="employee_id" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Warehouse/Gudang Utama</label>
                                <select wire:model="warehouse_id" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                    <option value="">Akses Global (Semua Gudang)</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Password {{ $isEdit ? '(Biarkan kosong jika tidak ganti)' : '' }}</label>
                                <input type="password" wire:model="password" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                                @error('password') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Pilih Role / Hak Akses</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    @foreach($roles as $role)
                                        <label class="flex items-center gap-2 p-2 border border-gray-100 rounded-lg hover:bg-gray-50 cursor-pointer transition-all">
                                            <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 transition-all">
                                            <span class="text-xs font-bold text-gray-700 uppercase tracking-tight">{{ str_replace('-', ' ', $role->name) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedRoles') <span class="text-[10px] text-red-500 italic font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2 flex items-center gap-2 pt-2">
                                <input type="checkbox" wire:model="is_active" id="user_is_active" class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 transition-all">
                                <label for="user_is_active" class="text-sm font-bold text-gray-700">User ini aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button wire:click="save" class="px-6 py-2 bg-brand-500 text-white text-sm font-bold rounded-xl hover:bg-brand-600 shadow-md shadow-brand-200 transition-all">
                            {{ $isEdit ? 'Update User' : 'Simpan User' }}
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
