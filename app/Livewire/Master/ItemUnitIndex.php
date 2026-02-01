<?php

namespace App\Livewire\Master;

use App\Models\ItemUnit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ItemUnitIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEdit = false;
    public $unitId;

    public $code, $name, $is_active = true;

    protected $queryString = ['search'];

    public function render()
    {
        $units = ItemUnit::where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.master.item-unit-index', [
            'units' => $units
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
        $this->code = '';
        $this->name = '';
        $this->is_active = true;
        $this->isEdit = false;
        $this->unitId = null;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:item_units,code' . ($this->isEdit ? ',' . $this->unitId : ''),
            'name' => 'required',
        ]);

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'updated_by' => auth()->id(),
        ];

        if ($this->isEdit) {
            ItemUnit::find($this->unitId)->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Satuan berhasil diperbarui']);
        } else {
            $data['created_by'] = auth()->id();
            ItemUnit::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Satuan berhasil ditambahkan']);
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $unit = ItemUnit::findOrFail($id);
        $this->unitId = $id;
        $this->code = $unit->code;
        $this->name = $unit->name;
        $this->is_active = $unit->is_active;
        $this->isEdit = true;
        $this->isOpen = true;
    }

    #[On('delete-unit')]
    public function delete($id)
    {
        ItemUnit::find($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Satuan berhasil dihapus']);
    }
}
