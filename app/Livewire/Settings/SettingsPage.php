<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class SettingsPage extends Component
{
    public $appName = 'TailAdmin Pharmacy';
    public $appAddress = 'Jl. Letkol Istiqlah No. 49, Banyuwangi';
    public $appPhone = '(0333) 421118';
    public $appEmail = 'rsudblambangan@banyuwangikab.go.id';
    
    // Simplification: just a placeholder UI for now since DB settings table isn't defined yet
    public function save()
    {
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengaturan aplikasi berhasil disimpan (Simulasi).']);
    }

    public function render()
    {
        return view('livewire.settings.settings-page');
    }
}
