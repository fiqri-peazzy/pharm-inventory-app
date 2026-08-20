<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center text-white text-xl font-black shrink-0">
            {{ strtoupper(substr($name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-lg font-black text-gray-900 dark:text-white">{{ $name }}</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->getRoleNames()->implode(', ') }}</p>
        </div>
    </div>

    <!-- Informasi Akun -->
    <div class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight dark:text-white">Informasi Akun</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ubah nama, kontak, dan data kepegawaian Anda.</p>
        </div>
        <form wire:submit="updateProfile" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Nama Lengkap</label>
                    <input type="text" wire:model="name"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('name') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('name')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Username</label>
                    <input type="text" wire:model="username"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('username') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('username')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Email</label>
                    <input type="email" wire:model="email"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('email') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('email')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">No. HP / Kontak</label>
                    <input type="text" wire:model="phone"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Employee ID (NIP)</label>
                    <input type="text" wire:model="employee_id"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">No. SIPA (Apoteker)</label>
                    <input type="text" wire:model="sipa_number"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
                </div>
            </div>
            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-brand-500 text-white text-sm font-black rounded-xl hover:bg-brand-600 shadow-lg shadow-brand-200 dark:shadow-none transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Keamanan / Ubah Password -->
    <div id="security" class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden scroll-mt-24">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight dark:text-white">Keamanan Akun</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ubah password login Anda secara berkala untuk keamanan.</p>
        </div>
        <form wire:submit="updatePassword" class="p-6 space-y-4">
            <div>
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Password Saat Ini</label>
                <input type="password" wire:model="currentPassword"
                    class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                    @error('currentPassword') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                @error('currentPassword')
                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Password Baru</label>
                    <input type="password" wire:model="newPassword"
                        class="block w-full px-4 py-2.5 border rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:bg-white transition-all dark:bg-white/[0.03]
                        @error('newPassword') border-red-500 focus:ring-red-500 dark:border-red-500 @else border-gray-200 focus:ring-brand-500 dark:border-gray-800 @enderror">
                    @error('newPassword')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.516 11.598c.75 1.334-.213 2.98-1.742 2.98H3.483c-1.53 0-2.493-1.646-1.743-2.98L8.257 3.1zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-wider ml-1 mb-1 dark:text-gray-500">Konfirmasi Password Baru</label>
                    <input type="password" wire:model="newPassword_confirmation"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all dark:bg-white/[0.03] dark:border-gray-800">
                </div>
            </div>
            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-gray-900 dark:bg-brand-500 text-white text-sm font-black rounded-xl hover:bg-gray-800 dark:hover:bg-brand-600 transition-all">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>
