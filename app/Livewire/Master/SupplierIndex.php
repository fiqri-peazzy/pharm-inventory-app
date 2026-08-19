<?php

namespace App\Livewire\Master;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class SupplierIndex extends Component
{
    use WithPagination;

    #[Url(as: 'search', history: false)]
    public $search = '';
    public $isOpen = false;
    public $isEdit = false;
    public $supplierId;

    // Form fields
    public $code, $name, $type, $address, $phone, $email, $contact_person;
    public $npwp, $tax_status = 'non_pkp', $payment_term = 30, $is_active = true;

    protected $queryString = ['search'];

    public function render()
    {
        $suppliers = Supplier::where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_person', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.master.supplier-index', [
            'suppliers' => $suppliers
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
        $this->supplierId = null;
        $this->code = '';
        $this->name = '';
        $this->type = '';
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->contact_person = '';
        $this->npwp = '';
        $this->tax_status = 'non_pkp';
        $this->payment_term = 30;
        $this->is_active = true;
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:suppliers,code' . ($this->isEdit ? ',' . $this->supplierId : ''),
            'name' => 'required',
            'type' => 'required',
        ]);

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'contact_person' => $this->contact_person,
            'npwp' => $this->npwp,
            'tax_status' => $this->tax_status,
            'payment_term' => $this->payment_term,
            'is_active' => $this->is_active,
            'updated_by' => auth()->id(),
        ];

        if ($this->isEdit) {
            Supplier::find($this->supplierId)->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Supplier berhasil diperbarui']);
        } else {
            $data['created_by'] = auth()->id();
            Supplier::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Supplier berhasil ditambahkan']);
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $id;
        $this->code = $supplier->code;
        $this->name = $supplier->name;
        $this->type = $supplier->type;
        $this->address = $supplier->address;
        $this->phone = $supplier->phone;
        $this->email = $supplier->email;
        $this->contact_person = $supplier->contact_person;
        $this->npwp = $supplier->npwp;
        $this->tax_status = $supplier->tax_status;
        $this->payment_term = $supplier->payment_term;
        $this->is_active = $supplier->is_active;
        $this->isEdit = true;
        $this->isOpen = true;
    }

    #[On('delete-supplier')]
    public function delete($id)
    {
        Supplier::find($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Supplier berhasil dihapus']);
    }
}
