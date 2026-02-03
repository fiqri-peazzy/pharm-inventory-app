<?php

namespace App\Livewire\Master;

use App\Models\ServiceUnit;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceUnitIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $selected_id;

    public $code, $name, $type, $category, $default_warehouse_id, $building, $floor, $is_active = true;

    protected $rules = [
        'code' => 'required|unique:service_units,code',
        'name' => 'required',
        'type' => 'required|in:poli,ruangan,instalasi',
        'default_warehouse_id' => 'nullable|exists:warehouses,id',
    ];

    public function render()
    {
        return view('livewire.master.service-unit-index', [
            'serviceUnits' => ServiceUnit::with('defaultWarehouse')
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
            'warehouses' => Warehouse::active()->get(),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->code = '';
        $this->name = '';
        $this->type = '';
        $this->category = '';
        $this->default_warehouse_id = '';
        $this->building = '';
        $this->floor = '';
        $this->is_active = true;
        $this->selected_id = null;
    }

    public function store()
    {
        $validationRules = $this->rules;
        if ($this->selected_id) {
            $validationRules['code'] = 'required|unique:service_units,code,' . $this->selected_id;
        }

        $this->validate($validationRules);

        ServiceUnit::updateOrCreate(['id' => $this->selected_id], [
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'category' => $this->category,
            'default_warehouse_id' => $this->default_warehouse_id ?: null,
            'building' => $this->building,
            'floor' => $this->floor,
            'is_active' => $this->is_active,
            'created_by' => $this->selected_id ? null : auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->selected_id ? 'Unit Layanan berhasil diperbarui' : 'Unit Layanan berhasil ditambahkan'
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $unit = ServiceUnit::findOrFail($id);
        $this->selected_id = $id;
        $this->code = $unit->code;
        $this->name = $unit->name;
        $this->type = $unit->type;
        $this->category = $unit->category;
        $this->default_warehouse_id = $unit->default_warehouse_id;
        $this->building = $unit->building;
        $this->floor = $unit->floor;
        $this->is_active = $unit->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        ServiceUnit::find($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Unit Layanan berhasil dihapus']);
    }
}
