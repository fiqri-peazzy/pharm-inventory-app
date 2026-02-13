<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleIndex extends Component
{
    public function render()
    {
        return view('livewire.master.role-index', [
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permissionsCount' => Permission::count(),
        ]);
    }
}
