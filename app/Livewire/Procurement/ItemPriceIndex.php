<?php

namespace App\Livewire\Procurement;

use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class ItemPriceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $selected_id;
    
    // Form properties
    public $item_id;
    public $supplier_id;
    public $price_type = 'e-catalog';
    public $price;
    public $ppn_percentage = 11;
    public $effective_date;
    public $end_date;
    public $is_active = true;

    protected $rules = [
        'item_id' => 'required|exists:items,id',
        'supplier_id' => 'required|exists:suppliers,id',
        'price_type' => 'required|string',
        'price' => 'required|numeric|min:0',
        'ppn_percentage' => 'required|numeric|min:0|max:100',
        'effective_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:effective_date',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->effective_date = date('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->selected_id = $id;
        $price = ItemPrice::findOrFail($id);
        
        $this->item_id = $price->item_id;
        $this->supplier_id = $price->supplier_id;
        $this->price_type = $price->price_type;
        $this->price = $price->price;
        $this->ppn_percentage = $price->ppn_percentage;
        $this->effective_date = $price->effective_date;
        $this->end_date = $price->end_date;
        $this->is_active = $price->is_active;

        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        ItemPrice::updateOrCreate(
            ['id' => $this->selected_id],
            [
                'item_id' => $this->item_id,
                'supplier_id' => $this->supplier_id,
                'price_type' => $this->price_type,
                'price' => $this->price,
                'ppn_percentage' => $this->ppn_percentage,
                'effective_date' => $this->effective_date,
                'end_date' => $this->end_date,
                'is_active' => $this->is_active,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success', 
            'message' => $this->selected_id ? 'Harga berhasil diperbarui' : 'Harga berhasil ditambahkan'
        ]);

        $this->closeModal();
    }

    #[On('delete-price')]
    public function delete($id)
    {
        ItemPrice::find($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Harga berhasil dihapus']);
    }

    public function resetForm()
    {
        $this->selected_id = null;
        $this->item_id = null;
        $this->supplier_id = null;
        $this->price_type = 'e-catalog';
        $this->price = null;
        $this->ppn_percentage = 11;
        $this->effective_date = date('Y-m-d');
        $this->end_date = null;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function render()
    {
        $prices = ItemPrice::with(['item', 'supplier'])
            ->where(function($query) {
                $query->whereHas('item', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('supplier', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.procurement.item-price-index', [
            'prices' => $prices,
            'items' => Item::active()->get(),
            'suppliers' => Supplier::active()->get(),
        ]);
    }
}
