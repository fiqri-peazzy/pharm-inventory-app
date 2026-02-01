<?php

namespace App\Livewire\Procurement;

use App\Models\Item;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class PurchaseRequestIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $selected_id;

    // Header PR
    public $request_number;
    public $warehouse_id;
    public $request_date;
    public $period_month;
    public $period_year;
    public $status = 'draft';
    public $notes;

    // Details PR
    public $items = []; // Array of {item_id, requested_qty, notes, current_stock, average_usage}

    protected $rules = [
        'warehouse_id' => 'required|exists:warehouses,id',
        'request_date' => 'required|date',
        'period_month' => 'required|integer|min:1|max:12',
        'period_year' => 'required|integer',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.requested_qty' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->request_date = date('Y-m-d');
        $this->period_month = date('m');
        $this->period_year = date('Y');
    }

    public function generateRequestNumber()
    {
        $prefix = 'PR/' . date('Ym') . '/';
        $lastRequest = PurchaseRequest::where('request_number', 'like', $prefix . '%')
            ->orderBy('request_number', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = intval(substr($lastRequest->request_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $this->request_number = $prefix . $newNumber;
    }

    public function create()
    {
        $this->resetForm();
        $this->generateRequestNumber();
        $this->addItem();
        $this->isOpen = true;
    }

    public function addItem()
    {
        $this->items[] = [
            'item_id' => '',
            'requested_qty' => 1,
            'notes' => '',
            'current_stock' => 0,
            'average_usage' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function store()
    {
        $this->validate();

        $pr = PurchaseRequest::create([
            'request_number' => $this->request_number,
            'warehouse_id' => $this->warehouse_id,
            'request_date' => $this->request_date,
            'period_month' => $this->period_month,
            'period_year' => $this->period_year,
            'status' => 'pending', // Auto-submit to pending
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            $pr->details()->create([
                'item_id' => $item['item_id'],
                'requested_qty' => $item['requested_qty'],
                'notes' => $item['notes'],
                'current_stock' => $item['current_stock'] ?? 0,
                'average_usage' => $item['average_usage'] ?? 0,
            ]);
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Permintaan Barang berhasil dikirim']);
        $this->closeModal();
    }

    public function resetForm()
    {
        $this->selected_id = null;
        $this->request_number = null;
        $this->warehouse_id = null;
        $this->request_date = date('Y-m-d');
        $this->period_month = date('m');
        $this->period_year = date('Y');
        $this->notes = null;
        $this->items = [];
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function render()
    {
        $requests = PurchaseRequest::with(['warehouse', 'creator'])
            ->where('request_number', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.procurement.purchase-request-index', [
            'requests' => $requests,
            'available_items' => Item::active()->get(),
            'warehouses' => Warehouse::active()->get(),
        ]);
    }
}
