<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

class SettingsPage extends Component
{
    public $appName = '';
    public $hospitalName = '';
    public $appAddress = '';
    public $appPhone = '';
    public $appEmail = '';

    protected function rules()
    {
        return [
            'appName' => ['required', 'string', 'max:255'],
            'hospitalName' => ['nullable', 'string', 'max:255'],
            'appAddress' => ['nullable', 'string', 'max:500'],
            'appPhone' => ['nullable', 'string', 'max:50'],
            'appEmail' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function mount()
    {
        $setting = Setting::current();

        $this->appName = $setting->app_name;
        $this->hospitalName = $setting->hospital_name;
        $this->appAddress = $setting->address;
        $this->appPhone = $setting->phone;
        $this->appEmail = $setting->email;
    }

    public function save()
    {
        $validated = $this->validate();

        $setting = Setting::current();
        $setting->update([
            'app_name' => $validated['appName'],
            'hospital_name' => $validated['hospitalName'] ?: $validated['appName'],
            'address' => $validated['appAddress'],
            'phone' => $validated['appPhone'],
            'email' => $validated['appEmail'],
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengaturan aplikasi berhasil disimpan.']);
    }

    public function render()
    {
        return view('livewire.settings.settings-page');
    }
}
