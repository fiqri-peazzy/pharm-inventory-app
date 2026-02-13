<?php

namespace App\Livewire\Master;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    
    // Modal states
    public $showModal = false;
    public $isEdit = false;
    public $userId;

    // Form data
    public $name, $username, $email, $employee_id, $phone, $warehouse_id, $password, $is_active = true, $selectedRoles = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $this->userId,
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'selectedRoles' => 'required|array|min:1',
        ];
    }

    public function render()
    {
        $users = User::with(['roles', 'warehouse'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master.user-index', [
            'users' => $users,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->userId = $id;
        $user = User::findOrFail($id);
        
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->employee_id = $user->employee_id;
        $this->phone = $user->phone;
        $this->warehouse_id = $user->warehouse_id;
        $this->is_active = $user->is_active;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'employee_id' => $this->employee_id,
            'phone' => $this->phone,
            'warehouse_id' => $this->warehouse_id ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            $user = User::find($this->userId);
            $user->update($data);
            $user->syncRoles($this->selectedRoles);
            $msg = 'User berhasil diperbarui.';
        } else {
            $user = User::create($data);
            $user->assignRole($this->selectedRoles);
            $msg = 'User baru berhasil ditambahkan.';
        }

        $this->showModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
    }

    public function toggleStatus($id)
    {
        if ($id == auth()->id()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak bisa menonaktifkan akun sendiri.']);
            return;
        }

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status user diperbarui.']);
    }

    private function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->employee_id = '';
        $this->phone = '';
        $this->warehouse_id = null;
        $this->password = '';
        $this->is_active = true;
        $this->selectedRoles = [];
    }
}
