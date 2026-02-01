<?php

namespace App\Livewire\Master;

use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class WarehouseIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEdit = false;
    public $warehouseId;

    // Form fields
    public $code, $name, $type, $is_main = false, $is_active = true, $pic_name, $pic_phone, $address;

    protected $queryString = ['search'];

    public function render()
    {
        $warehouses = Warehouse::where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.master.warehouse-index', [
            'warehouses' => $warehouses
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetForm()
    {
        $this->warehouseId = null;
        $this->code = '';
        $this->name = '';
        $this->type = '';
        $this->is_main = false;
        $this->is_active = true;
        $this->pic_name = '';
        $this->pic_phone = '';
        $this->address = '';
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:warehouses,code' . ($this->isEdit ? ',' . $this->warehouseId : ''),
            'name' => 'required',
            'type' => 'required',
        ]);

        if ($this->is_main) {
            Warehouse::where('is_main', true)->update(['is_main' => false]);
        }

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'is_main' => $this->is_main,
            'is_active' => $this->is_active,
            'pic_name' => $this->pic_name,
            'pic_phone' => $this->pic_phone,
            'address' => $this->address,
            'updated_by' => auth()->id(),
        ];

        if ($this->isEdit) {
            Warehouse::find($this->warehouseId)->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Gudang berhasil diperbarui']);
        } else {
            $data['created_by'] = auth()->id();
            Warehouse::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Gudang berhasil ditambahkan']);
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $this->warehouseId = $id;
        $this->code = $warehouse->code;
        $this->name = $warehouse->name;
        $this->type = $warehouse->type;
        $this->is_main = $warehouse->is_main;
        $this->is_active = $warehouse->is_active;
        $this->pic_name = $warehouse->pic_name;
        $this->pic_phone = $warehouse->pic_phone;
        $this->address = $warehouse->address;
        $this->isEdit = true;
        $this->isOpen = true;
    }

    #[On('delete-warehouse')]
    public function delete($id)
    {
        $warehouse = Warehouse::find($id);
        if ($warehouse->is_main) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gudang utama tidak boleh dihapus']);
            return;
        }
        $warehouse->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Gudang berhasil dihapus']);
    }
}
