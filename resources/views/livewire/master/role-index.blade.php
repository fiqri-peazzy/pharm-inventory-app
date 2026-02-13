<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">{{ str_replace('-', ' ', $role->name) }}</h3>
                        <p class="text-[10px] text-gray-500 font-mono">Guard: {{ $role->guard_name }}</p>
                    </div>
                    <div class="px-3 py-1 bg-brand-50 text-brand-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-brand-100">
                        {{ $role->permissions->count() }} Permisi
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @forelse($role->permissions as $perm)
                            <span class="px-2 py-1 rounded bg-gray-50 text-[9px] font-bold text-gray-600 border border-gray-100">
                                {{ $perm->name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic font-medium">Tidak ada permisi khusus atau akses penuh (Internal).</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
