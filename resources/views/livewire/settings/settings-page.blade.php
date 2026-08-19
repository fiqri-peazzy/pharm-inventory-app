<div class="space-y-6 max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-white/[0.03] dark:border-gray-800">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 dark:border-gray-800">
            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight dark:text-white">Informasi Unit Pelaksana (RSUD/Puskesmas)</h3>
            <p class="text-xs text-gray-500 font-medium dark:text-gray-400">Pengaturan identitas aplikasi yang akan muncul di kop laporan dan struk.</p>
        </div>
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Nama Aplikasi / Instansi</label>
                    <input type="text" wire:model="appName"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('appName') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('appName')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Nama Rumah Sakit/Instansi (untuk dokumen)</label>
                    <input type="text" wire:model="hospitalName"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('hospitalName') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('hospitalName')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Nomor Telepon</label>
                    <input type="text" wire:model="appPhone"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('appPhone') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('appPhone')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Email Resmi</label>
                    <input type="email" wire:model="appEmail"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('appEmail') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('appEmail')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Alamat Lengkap</label>
                    <textarea wire:model="appAddress" rows="3"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('appAddress') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror"></textarea>
                    @error('appAddress')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Logo Instansi (untuk KOP surat / template Excel)</label>
                    <div class="flex items-center gap-4">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="w-16 h-16 object-contain rounded-lg border border-gray-200 bg-white p-1 dark:border-gray-700">
                        @elseif ($currentLogoPath)
                            <img src="{{ asset($currentLogoPath) }}" class="w-16 h-16 object-contain rounded-lg border border-gray-200 bg-white p-1 dark:border-gray-700">
                        @else
                            <div class="w-16 h-16 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-gray-300 dark:border-gray-700 dark:text-gray-600">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>
                        @endif
                        <label class="cursor-pointer px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold uppercase tracking-wider rounded-lg transition-all dark:bg-white/[0.05] dark:text-gray-300 dark:hover:bg-white/[0.08]">
                            <span wire:loading.remove wire:target="logo">Pilih File Logo</span>
                            <span wire:loading wire:target="logo">Mengunggah...</span>
                            <input type="file" wire:model="logo" accept="image/*" class="hidden">
                        </label>
                    </div>
                    @error('logo')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-[10px] text-gray-400">Format PNG/JPG, maksimal 2MB. Logo ini otomatis muncul di kop template Excel import stok awal.</p>
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

    <div class="bg-gray-100 p-6 rounded-2xl border-2 border-dashed border-gray-200 dark:bg-gray-800 dark:border-gray-800">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-xl text-gray-400 dark:bg-white/[0.03] dark:text-gray-500">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-gray-900 uppercase dark:text-white">Security & Maintenance</h4>
                <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">Pengaturan lanjutan seperti backup database dan log cleanup akan tersedia di versi berikutnya.</p>
            </div>
        </div>
    </div>
</div>
