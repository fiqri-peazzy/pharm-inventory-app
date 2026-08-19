<?php

namespace App\Livewire\AI;

use App\Services\AI\ChatAssistantService;
use Livewire\Component;

class ChatAssistant extends Component
{
    /** @var array<int, array{role:string, text:string}> */
    public array $messages = [];
    public string $newMessage = '';
    public bool $isThinking = false;

    protected $rules = [
        'newMessage' => 'required|string|max:1000',
    ];

    public function mount()
    {
        $this->messages[] = [
            'role' => 'assistant',
            'text' => 'Halo! Saya asisten AI sistem ini. Tanya apa saja soal cara pakai aplikasi — misalnya "bagaimana cara input penerimaan barang?" atau "di mana saya bisa lihat stok yang mau kadaluarsa?".',
        ];
    }

    public function send(ChatAssistantService $service)
    {
        $this->validate();

        $userMessage = trim($this->newMessage);
        $this->messages[] = ['role' => 'user', 'text' => $userMessage];
        $this->newMessage = '';
        $this->isThinking = true;

        // History excludes the greeting + the message just appended (sent separately).
        $history = collect($this->messages)
            ->slice(1, -1)
            ->values()
            ->all();

        $reply = $service->reply($history, $userMessage);

        $this->messages[] = [
            'role' => 'assistant',
            'text' => $reply ?? 'Maaf, asisten AI sedang tidak dapat dihubungi. Silakan coba lagi sebentar lagi, atau buka Manual Book untuk panduan lengkap.',
        ];
        $this->isThinking = false;

        $this->dispatch('chat-message-added');
    }

    public function clearChat()
    {
        $this->messages = [];
        $this->mount();
    }

    public function render()
    {
        return view('livewire.ai.chat-assistant');
    }
}
