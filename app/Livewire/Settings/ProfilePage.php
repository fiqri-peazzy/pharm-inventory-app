<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProfilePage extends Component
{
    public $name = '';
    public $username = '';
    public $email = '';
    public $phone = '';
    public $employee_id = '';
    public $sipa_number = '';

    public $currentPassword = '';
    public $newPassword = '';
    public $newPassword_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->employee_id = $user->employee_id;
        $this->sipa_number = $user->sipa_number;
    }

    protected function profileRules()
    {
        $userId = auth()->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'employee_id' => ['nullable', 'string', 'max:100'],
            'sipa_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function updateProfile()
    {
        $validated = $this->validate($this->profileRules());

        auth()->user()->update($validated);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Profil berhasil diperbarui.']);
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => ['required'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($this->currentPassword, auth()->user()->password)) {
            $this->addError('currentPassword', 'Password saat ini tidak sesuai.');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPassword_confirmation = '';

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Password berhasil diubah.']);
    }

    public function render()
    {
        return view('livewire.settings.profile-page');
    }
}
