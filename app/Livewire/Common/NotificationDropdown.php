<?php

namespace App\Livewire\Common;

use App\Services\NotificationService;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public array $items = [];

    public function mount(NotificationService $service)
    {
        $this->items = $service->forUser(auth()->user());
    }

    #[On('refresh-notifications')]
    public function refresh(NotificationService $service)
    {
        $this->items = $service->forUser(auth()->user());
    }

    public function render()
    {
        return view('livewire.common.notification-dropdown');
    }
}
