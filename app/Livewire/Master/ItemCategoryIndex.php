<?php

namespace App\Livewire\Master;

use App\Models\ItemCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Imports\ItemCategoryImport;
use App\Exports\ItemCategoryExport;
use Maatwebsite\Excel\Facades\Excel;

class ItemCategoryIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $isOpen = false;
    public $isImportModalOpen = false;
    public $isEdit = false;
    public $categoryId;

    public $importFile;

    // Form fields
    public $code, $name, $type, $parent_id, $is_active = true;

    protected $queryString = ['search'];

    public function render()
    {
        $categories = ItemCategory::with('parent')
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $parentCategories = ItemCategory::whereNull('parent_id')->get();

        return view('livewire.master.item-category-index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories
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
        $this->type = '';
        $this->parent_id = null;
        $this->is_active = true;
        $this->isEdit = false;
        $this->categoryId = null;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:item_categories,code' . ($this->isEdit ? ',' . $this->categoryId : ''),
            'name' => 'required',
            'type' => 'required',
        ]);

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'parent_id' => $this->parent_id ?: null,
            'is_active' => $this->is_active,
            'updated_by' => auth()->id(),
        ];

        if ($this->isEdit) {
            ItemCategory::find($this->categoryId)->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kategori berhasil diperbarui']);
        } else {
            $data['created_by'] = auth()->id();
            ItemCategory::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kategori berhasil ditambahkan']);
        }

        $this->closeModal();
        $this->resetForm();
    }

    public function edit($id)
    {
        $category = ItemCategory::findOrFail($id);
        $this->categoryId = $id;
        $this->code = $category->code;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->parent_id = $category->parent_id;
        $this->is_active = $category->is_active;
        $this->isEdit = true;
        $this->isOpen = true;
    }

    #[On('delete-category')]
    public function delete($id)
    {
        ItemCategory::find($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kategori berhasil dihapus']);
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
            Excel::import(new ItemCategoryImport, $this->importFile->getRealPath());
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Data kategori berhasil diimport']);
            $this->closeImportModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal mengimport data: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new ItemCategoryExport, 'template_kategori.xlsx');
    }
}
