<div x-data="{ open: false }" class="fixed bottom-6 right-28 z-[99998]" @chat-message-added.window="$nextTick(() => { const el = $refs.scrollArea; if (el) el.scrollTop = el.scrollHeight; })">

    <!-- Floating trigger button -->
    <button @click="open = !open"
        class="ai-glow-badge w-14 h-14 rounded-full shadow-xl flex items-center justify-center bg-gradient-to-br from-indigo-600 to-violet-600 text-white hover:scale-105 active:scale-95 transition-transform"
        title="Tanya Asisten AI">
        <svg x-show="!open" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.117 0-2.185-.183-3.168-.52L3 21l1.606-4.286C3.586 15.362 3 13.74 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <svg x-show="open" x-cloak width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat panel -->
    <div x-show="open" x-cloak style="display: none"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-3"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute bottom-[68px] right-0 w-[92vw] max-w-[380px] h-[520px] max-h-[75vh] rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl shadow-indigo-950/10 flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-3.5 bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center gap-2.5 shrink-0 relative overflow-hidden">
            <div class="absolute inset-0 opacity-40" style="background: radial-gradient(circle at 90% 0%, rgb(255 255 255 / 0.25), transparent 55%);"></div>
            <div class="relative w-8 h-8 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4" />
                    <circle cx="12" cy="12" r="4" />
                </svg>
            </div>
            <div class="relative flex-1 min-w-0">
                <p class="text-sm font-black text-white leading-tight">Asisten AI</p>
                <p class="text-[10px] text-white/70 flex items-center gap-1">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
                    </span>
                    Siap membantu
                </p>
            </div>
            <button wire:click="clearChat" class="relative text-white/60 hover:text-white hover:bg-white/10 rounded-lg p-1.5 transition-colors" title="Mulai obrolan baru">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </button>
        </div>

        <!-- Messages -->
        <div x-ref="scrollArea" class="flex-1 overflow-y-auto custom-scrollbar px-3.5 py-3 space-y-3">
            @foreach ($messages as $msg)
                <div class="ai-fade-up flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs leading-relaxed whitespace-pre-line
                        {{ $msg['role'] === 'user'
                            ? 'bg-brand-500 text-white rounded-br-sm'
                            : 'bg-gray-100 dark:bg-white/[0.06] text-gray-700 dark:text-gray-300 rounded-bl-sm' }}">
                        {{ $msg['text'] }}
                    </div>
                </div>
            @endforeach

            @if ($isThinking)
                <div class="ai-fade-up flex justify-start">
                    <div class="bg-gray-100 dark:bg-white/[0.06] rounded-2xl rounded-bl-sm px-3.5 py-3 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500 animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500 animate-bounce" style="animation-delay: 120ms"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500 animate-bounce" style="animation-delay: 240ms"></span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Input -->
        <form wire:submit.prevent="send" class="p-3 border-t border-gray-100 dark:border-gray-800 shrink-0">
            @error('newMessage') <p class="text-[10px] text-red-500 mb-1.5">{{ $message }}</p> @enderror
            <div class="flex items-end gap-2">
                <textarea wire:model="newMessage" rows="1" placeholder="Tanya cara pakai sistem ini..."
                    wire:keydown.enter.prevent="send"
                    @disabled($isThinking)
                    class="flex-1 resize-none max-h-24 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-white/[0.03] px-3 py-2 text-xs text-gray-800 dark:text-white/90 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 disabled:opacity-60"></textarea>
                <button type="submit" wire:loading.attr="disabled" wire:target="send" @disabled($isThinking)
                    class="w-9 h-9 rounded-xl bg-brand-500 hover:bg-brand-600 text-white flex items-center justify-center shrink-0 disabled:opacity-50 active:scale-95 transition-all">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6" /></svg>
                </button>
            </div>
        </form>
    </div>
</div>
