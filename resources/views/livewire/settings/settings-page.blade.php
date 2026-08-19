<div class="space-y-6 max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Informasi Unit Pelaksana (RSUD/Puskesmas)</h3>
            <p class="text-xs text-gray-500 font-medium">Pengaturan identitas aplikasi yang akan muncul di kop laporan dan struk.</p>
        </div>
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Nama Aplikasi / Instansi</label>
                    <input type="text" wire:model="appName" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Nama Rumah Sakit/Instansi (untuk dokumen)</label>
                    <input type="text" wire:model="hospitalName" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Nomor Telepon</label>
                    <input type="text" wire:model="appPhone" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Email Resmi</label>
                    <input type="email" wire:model="appEmail" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1">Alamat Lengkap</label>
                    <textarea wire:model="appAddress" rows="3" class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"></textarea>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button wire:click="save" class="px-8 py-3 bg-brand-500 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-brand-600 shadow-lg shadow-brand-200 transition-all flex items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    <div class="bg-gray-100 p-6 rounded-2xl border-2 border-dashed border-gray-200">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-xl text-gray-400">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-gray-900 uppercase">Security & Maintenance</h4>
                <p class="text-xs text-gray-500 mt-1">Pengaturan lanjutan seperti backup database dan log cleanup akan tersedia di versi berikutnya.</p>
            </div>
        </div>
    </div>
</div>
