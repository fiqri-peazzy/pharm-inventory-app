<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class SettingsPage extends Component
{
    use WithFileUploads;

    public $appName = '';
    public $hospitalName = '';
    public $appAddress = '';
    public $appPhone = '';
    public $appEmail = '';
    public $logo;
    public $currentLogoPath = null;

    protected function rules()
    {
        return [
            'appName' => ['required', 'string', 'max:255'],
            'hospitalName' => ['nullable', 'string', 'max:255'],
            'appAddress' => ['nullable', 'string', 'max:500'],
            'appPhone' => ['nullable', 'string', 'max:50'],
            'appEmail' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
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
        $this->currentLogoPath = $setting->logo_path;
    }

    public function save()
    {
        $validated = $this->validate();

        $setting = Setting::current();

        $data = [
            'app_name' => $validated['appName'],
            'hospital_name' => $validated['hospitalName'] ?: $validated['appName'],
            'address' => $validated['appAddress'],
            'phone' => $validated['appPhone'],
            'email' => $validated['appEmail'],
        ];

        if ($this->logo) {
            if ($setting->logo_path && is_file(public_path($setting->logo_path))) {
                @unlink(public_path($setting->logo_path));
            }

            $filename = 'logo-instansi-' . time() . '.' . $this->logo->getClientOriginalExtension();
            $destination = public_path('images/logo');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $this->logo->move($destination, $filename);
            $data['logo_path'] = 'images/logo/' . $filename;
        }

        $setting->update($data);
        $this->currentLogoPath = $setting->fresh()->logo_path;
        $this->logo = null;

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Pengaturan aplikasi berhasil disimpan.']);
    }

    public function render()
    {
        return view('livewire.settings.settings-page');
    }
}
