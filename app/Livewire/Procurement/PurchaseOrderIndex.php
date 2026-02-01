<?php

namespace App\Livewire\Procurement;

use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrderIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $selected_id;

    // Header PO
    public $po_number;
    public $purchase_request_id;
    public $supplier_id;
    public $warehouse_id;
    public $po_date;
    public $expected_delivery_date;
    public $payment_term = 30;
    public $notes;

    // Totals
    public $total_amount = 0;
    public $ppn_amount = 0;
    public $discount_amount = 0;
    public $grand_total = 0;

    // Details PO
    public $items = []; // Array of {item_id, qty_ordered, purchase_price, ppn_percentage, discount_percentage, subtotal}

    protected $rules = [
        'po_number' => 'required|unique:purchase_orders,po_number',
        'supplier_id' => 'required|exists:suppliers,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'po_date' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.qty_ordered' => 'required|integer|min:1',
        'items.*.purchase_price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->po_date = date('Y-m-d');
        $this->expected_delivery_date = date('Y-m-d', strtotime('+7 days'));
    }

    public function generatePONumber()
    {
        $prefix = 'PO/' . date('Ym') . '/';
        $lastPO = PurchaseOrder::where('po_number', 'like', $prefix . '%')
            ->orderBy('po_number', 'desc')
            ->first();

        if ($lastPO) {
            $lastNumber = intval(substr($lastPO->po_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $this->po_number = $prefix . $newNumber;
    }

    public function create()
    {
        $this->resetForm();
        $this->generatePONumber();
        $this->addItem();
        $this->isOpen = true;
    }

    public function addItem()
    {
        $this->items[] = [
            'item_id' => '',
            'qty_ordered' => 1,
            'purchase_price' => 0,
            'ppn_percentage' => 11,
            'discount_percentage' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function loadFromPR()
    {
        if (!$this->purchase_request_id) return;

        $pr = PurchaseRequest::with('details.item')->find($this->purchase_request_id);
        if ($pr) {
            $this->items = [];
            foreach ($pr->details as $detail) {
                if ($detail->approved_qty > 0) {
                    $this->items[] = [
                        'item_id' => $detail->item_id,
                        'qty_ordered' => $detail->approved_qty,
                        'purchase_price' => 0,
                        'ppn_percentage' => 11,
                        'discount_percentage' => 0,
                        'subtotal' => 0,
                    ];
                }
            }
            $this->warehouse_id = $pr->warehouse_id;
            $this->calculateTotals();
        }
    }

    public function fetchPrice($index)
    {
        $itemId = $this->items[$index]['item_id'];
        $supplierId = $this->supplier_id;

        if (!$itemId || !$supplierId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih Item dan Supplier terlebih dahulu']);
            return;
        }

        $price = ItemPrice::where('item_id', $itemId)
            ->where('supplier_id', $supplierId)
            ->where('is_active', true)
            ->first();

        if ($price) {
            $this->items[$index]['purchase_price'] = $price->price;
            $this->items[$index]['ppn_percentage'] = $price->ppn_percentage;
            $this->calculateTotals();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Harga E-Catalog berhasil ditarik']);
        } else {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Harga E-Catalog tidak ditemukan']);
        }
    }

    public function calculateTotals()
    {
        $this->total_amount = 0;
        $this->ppn_amount = 0;
        
        foreach ($this->items as $index => &$item) {
            $subtotal_raw = $item['qty_ordered'] * ($item['purchase_price'] ?? 0);
            $discount = $subtotal_raw * (($item['discount_percentage'] ?? 0) / 100);
            $subtotal_after_discount = $subtotal_raw - $discount;
            $ppn = $subtotal_after_discount * (($item['ppn_percentage'] ?? 0) / 100);
            
            $item['subtotal'] = $subtotal_after_discount + $ppn;
            
            $this->total_amount += $subtotal_after_discount;
            $this->ppn_amount += $ppn;
        }

        $this->grand_total = $this->total_amount + $this->ppn_amount - ($this->discount_amount ?? 0);
    }

    public function store()
    {
        $this->calculateTotals();
        $this->validate();

        $po = PurchaseOrder::create([
            'po_number' => $this->po_number,
            'purchase_request_id' => $this->purchase_request_id,
            'supplier_id' => $this->supplier_id,
            'warehouse_id' => $this->warehouse_id,
            'po_date' => $this->po_date,
            'expected_delivery_date' => $this->expected_delivery_date,
            'payment_term' => $this->payment_term,
            'total_amount' => $this->total_amount,
            'ppn_amount' => $this->ppn_amount,
            'discount_amount' => $this->discount_amount,
            'grand_total' => $this->grand_total,
            'status' => 'pending',
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            $po->details()->create([
                'item_id' => $item['item_id'],
                'qty_ordered' => $item['qty_ordered'],
                'purchase_price' => $item['purchase_price'],
                'ppn_percentage' => $item['ppn_percentage'],
                'ppn_amount' => ($item['qty_ordered'] * $item['purchase_price']) * ($item['ppn_percentage'] / 100),
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'subtotal' => $item['subtotal'],
            ]);
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Pesanan Barang (PO) berhasil dibuat']);
        $this->closeModal();
    }

    public function resetForm()
    {
        $this->po_number = null;
        $this->purchase_request_id = null;
        $this->supplier_id = null;
        $this->warehouse_id = null;
        $this->po_date = date('Y-m-d');
        $this->expected_delivery_date = date('Y-m-d', strtotime('+7 days'));
        $this->payment_term = 30;
        $this->notes = null;
        $this->items = [];
        $this->total_amount = 0;
        $this->ppn_amount = 0;
        $this->discount_amount = 0;
        $this->grand_total = 0;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function render()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'warehouse', 'creator'])
            ->where('po_number', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.procurement.purchase-order-index', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => Supplier::active()->get(),
            'warehouses' => Warehouse::active()->get(),
            'approved_requests' => PurchaseRequest::where('status', 'approved')->get(),
            'available_items' => Item::active()->get(),
        ]);
    }
}
