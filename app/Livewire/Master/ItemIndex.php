<?php

namespace App\Livewire\Master;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Imports\ItemImport;
use App\Exports\ItemExport;
use Maatwebsite\Excel\Facades\Excel;

class ItemIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $isOpen = false;
    public $isImportModalOpen = false;
    public $isEdit = false;
    public $itemId;

    public $importFile;

    // Form fields
    public $code, $nie_number, $barcode, $name, $generic_name, $item_category_id;
    public $manufacturer, $item_unit_id;
    public $is_prescription = false, $is_consignment = false, $is_active = true;
    public $storage_condition = 'suhu_ruang', $fornas_status, $fornas_code, $notes;

    protected $queryString = ['search'];

    public function render()
    {
        $items = Item::with(['category', 'unit'])
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('generic_name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $categories = ItemCategory::active()->get();
        $units = ItemUnit::active()->get();

        return view('livewire.master.item-index', [
            'items' => $items,
            'categories' => $categories,
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
        $this->itemId = null;
        $this->code = '';
        $this->nie_number = '';
        $this->barcode = '';
        $this->name = '';
        $this->generic_name = '';
        $this->item_category_id = '';
        $this->manufacturer = '';
        $this->item_unit_id = '';
        $this->is_prescription = false;
        $this->is_consignment = false;
        $this->is_active = true;
        $this->storage_condition = 'suhu_ruang';
        $this->fornas_status = '';
        $this->fornas_code = '';
        $this->notes = '';
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:items,code' . ($this->isEdit ? ',' . $this->itemId : ''),
            'name' => 'required',
            'item_category_id' => 'required',
            'item_unit_id' => 'required',
        ]);

        $data = [
            'code' => $this->code,
            'nie_number' => $this->nie_number,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'generic_name' => $this->generic_name,
            'item_category_id' => $this->item_category_id,
            'manufacturer' => $this->manufacturer,
            'item_unit_id' => $this->item_unit_id,
            'is_prescription' => $this->is_prescription,
            'is_consignment' => $this->is_consignment,
            'is_active' => $this->is_active,
            'storage_condition' => $this->storage_condition,
            'fornas_status' => $this->fornas_status,
            'fornas_code' => $this->fornas_code,
            'notes' => $this->notes,
            'updated_by' => auth()->id(),
        ];

        if ($this->isEdit) {
            Item::find($this->itemId)->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Item berhasil diperbarui']);
        } else {
            $data['created_by'] = auth()->id();
            Item::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Item berhasil ditambahkan']);
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $this->itemId = $id;
        $this->code = $item->code;
        $this->nie_number = $item->nie_number;
        $this->barcode = $item->barcode;
        $this->name = $item->name;
        $this->generic_name = $item->generic_name;
        $this->item_category_id = $item->item_category_id;
        $this->manufacturer = $item->manufacturer;
        $this->item_unit_id = $item->item_unit_id;
        $this->is_prescription = $item->is_prescription;
        $this->is_consignment = $item->is_consignment;
        $this->is_active = $item->is_active;
        $this->storage_condition = $item->storage_condition;
        $this->fornas_status = $item->fornas_status;
        $this->fornas_code = $item->fornas_code;
        $this->notes = $item->notes;
        $this->isEdit = true;
        $this->isOpen = true;
    }

    #[On('delete-item')]
    public function delete($id)
    {
        Item::find($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Item berhasil dihapus']);
    }

    public function openImportModal()
    {
        $this->importFile = null;
        $this->isImportModalOpen = true;
    }

    public function closeImportModal()
    {
        $this->isImportModalOpen = false;
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new ItemImport, $this->importFile->getRealPath());
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Data item berhasil diimport']);
            $this->closeImportModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal mengimport data: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new ItemExport, 'template_item_obat.xlsx');
    }
}
