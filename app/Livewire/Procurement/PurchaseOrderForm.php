<?php

namespace App\Livewire\Procurement;

use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\Warehouse;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use DB;

class PurchaseOrderForm extends Component
{
    public $orderId;
    public $isEdit = false;

    // Header fields
    public $po_number;
    public $purchase_request_id;
    public $supplier_id;
    public $warehouse_id;
    public $po_date;
    public $expected_delivery_date;
    public $payment_term = 30; // Default 30 days
    public $notes;
    public $from_prs = []; // Multi-PR source

    // Totals
    public $total_amount = 0;
    public $total_discount = 0;
    public $total_ppn = 0;
    public $grand_total = 0;

    // Items table
    public $rows = []; // {item_id, item_name, item_code, qty_ordered, purchase_price, discount_percentage, discount_amount, ppn_percentage, ppn_amount, subtotal, notes}

    // List for selection
    public $approvedPRs = [];

    // UI states
    public $showItemModal = false;
    public $itemSearch = '';
    public $searchResults = [];

    protected function rules()
    {
        return [
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:po_date',
            'payment_term' => 'required|integer|min:0',
            'rows.*.item_id' => 'required',
            'rows.*.qty_ordered' => 'required|numeric|min:0.01',
            'rows.*.purchase_price' => 'required|numeric|min:0.01',
            'rows.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'rows.*.ppn_percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function mount($orderId = null)
    {
        $this->po_date = date('Y-m-d');
        $this->expected_delivery_date = date('Y-m-d', strtotime('+7 days'));
        $this->approvedPRs = PurchaseRequest::where('status', 'approved')->get();

        if ($orderId) {
            $this->orderId = $orderId;
            $this->isEdit = true;
            $this->loadOrder();
        } else {
            $this->generatePoNumber();
            $mainWh = Warehouse::where('is_main', true)->first();
            if ($mainWh) $this->warehouse_id = $mainWh->id;

            // Handle Multi-PR consolidation
            if (request()->has('from_prs')) {
                $prIds = explode(',', request('from_prs'));
                $this->from_prs = $prIds;
                $this->loadFromMultiplePRs($prIds);
            }
        }
    }

    public function loadFromMultiplePRs($prIds)
    {
        $prs = PurchaseRequest::with('details.item')->whereIn('id', $prIds)->get();
        if ($prs->isEmpty()) return;

        // Set Supplier & Warehouse from first PR
        $this->supplier_id = $prs->first()->supplier_id;
        $this->warehouse_id = $prs->first()->warehouse_id;

        $aggregatedItems = []; // item_id => [qty, item_object]

        foreach ($prs as $pr) {
            foreach ($pr->details as $detail) {
                if (!isset($aggregatedItems[$detail->item_id])) {
                    $aggregatedItems[$detail->item_id] = [
                        'qty' => 0,
                        'item' => $detail->item
                    ];
                }
                $aggregatedItems[$detail->item_id]['qty'] += ($detail->approved_qty ?? $detail->requested_qty);
            }
        }

        $this->rows = [];
        foreach ($aggregatedItems as $itemId => $data) {
            $this->addItem($itemId, $data['qty']);
        }

        $this->notes = "Gabungan dari PR: " . $prs->pluck('request_number')->implode(', ');
    }

    public function generatePoNumber()
    {
        $date = date('Y/m');
        $lastPo = PurchaseOrder::where('po_number', 'like', "PO/$date/%")->latest()->first();

        $number = 1;
        if ($lastPo) {
            $lastNum = explode('/', $lastPo->po_number);
            $number = (int)end($lastNum) + 1;
        }

        $this->po_number = "PO/$date/" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function updatedPurchaseRequestId($value)
    {
        if ($value) {
            $pr = PurchaseRequest::with('details.item', 'warehouse')->find($value);
            if ($pr) {
                $this->warehouse_id = $pr->warehouse_id;
                $this->rows = [];
                foreach ($pr->details as $detail) {
                    $this->addItem($detail->item_id, $detail->approved_qty ?? $detail->requested_qty);
                }
            }
        }
    }

    public function updatedSupplierId($value)
    {
        if ($value) {
            $supplier = Supplier::find($value);
            if ($supplier && $supplier->payment_term) {
                $this->payment_term = $supplier->payment_term;
            }
        }
    }

    public function loadOrder()
    {
        $order = PurchaseOrder::with('details.item')->findOrFail($this->orderId);

        if (!in_array($order->status, ['draft', 'sent'])) {
            return redirect()->route('procurement.orders.index')->with('error', 'Hanya PO Draft atau Sent yang dapat diubah.');
        }

        $this->rows = [];

        $this->po_number = $order->po_number;
        $this->purchase_request_id = $order->purchase_request_id;
        $this->supplier_id = $order->supplier_id;
        $this->warehouse_id = $order->warehouse_id;
        $this->po_date = $order->po_date;
        $this->expected_delivery_date = $order->expected_delivery_date;
        $this->payment_term = $order->payment_term;
        $this->notes = $order->notes;

        foreach ($order->details as $detail) {
            $this->rows[] = [
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'item_code' => $detail->item->code,
                'qty_ordered' => $detail->qty_ordered,
                'purchase_price' => $detail->purchase_price,
                'discount_percentage' => $detail->discount_percentage,
                'discount_amount' => $detail->discount_amount,
                'ppn_percentage' => $detail->ppn_percentage,
                'ppn_amount' => $detail->ppn_amount,
                'subtotal' => $detail->subtotal,
                'notes' => $detail->notes,
            ];
        }
        $this->calculateTotals();
    }

    public function updatedItemSearch()
    {
        if (strlen($this->itemSearch) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Item::where('name', 'like', '%' . $this->itemSearch . '%')
            ->orWhere('code', 'like', '%' . $this->itemSearch . '%')
            ->limit(10)
            ->get();
    }

    public function addItem($itemId, $qty = 1)
    {
        // Simple duplicate check
        foreach ($this->rows as $row) {
            if ($row['item_id'] == $itemId) return;
        }

        $item = Item::with(['prices' => function ($q) {
            if ($this->supplier_id) $q->where('supplier_id', $this->supplier_id);
            $q->latest('effective_date');
        }])->findOrFail($itemId);

        $price = $item->prices->first()->price ?? 0;

        $this->rows[] = [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'item_code' => $item->code,
            'qty_ordered' => $qty,
            'purchase_price' => $price,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'ppn_percentage' => 11, // Default 11%
            'ppn_amount' => 0,
            'subtotal' => 0,
            'notes' => '',
        ];

        $this->calculateTotals();
        $this->showItemModal = false;
        $this->itemSearch = '';
        $this->searchResults = [];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        $this->calculateTotals();
    }

    public function updatedRows()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->total_amount = 0;
        $this->total_discount = 0;
        $this->total_ppn = 0;

        foreach ($this->rows as $index => $row) {
            $qty = (float)($row['qty_ordered'] ?? 0);
            $price = (float)($row['purchase_price'] ?? 0);
            $disc_pct = (float)($row['discount_percentage'] ?? 0);
            $ppn_pct = (float)($row['ppn_percentage'] ?? 0);

            $gross = $qty * $price;
            $disc_amt = $gross * ($disc_pct / 100);
            $net_after_disc = $gross - $disc_amt;
            $ppn_amt = $net_after_disc * ($ppn_pct / 100);
            $row_subtotal = $net_after_disc + $ppn_amt;

            $this->rows[$index]['discount_amount'] = $disc_amt;
            $this->rows[$index]['ppn_amount'] = $ppn_amt;
            $this->rows[$index]['subtotal'] = $row_subtotal;

            $this->total_amount += $gross;
            $this->total_discount += $disc_amt;
            $this->total_ppn += $ppn_amt;
        }

        $this->grand_total = ($this->total_amount - $this->total_discount) + $this->total_ppn;
    }

    public function save($status = 'draft')
    {
        try {
            $this->validate();

            if (empty($this->rows)) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Minimal harus ada 1 item.']);
                return;
            }

            if ($this->grand_total <= 0) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Grand Total tidak boleh nol. Periksa kembali harga dan qty item.']);
                return;
            }

            DB::transaction(function () use ($status) {
                $data = [
                    'po_number' => $this->po_number,
                    'purchase_request_id' => $this->purchase_request_id ?: null,
                    'supplier_id' => $this->supplier_id,
                    'warehouse_id' => $this->warehouse_id,
                    'po_date' => $this->po_date,
                    'expected_delivery_date' => $this->expected_delivery_date,
                    'payment_term' => $this->payment_term,
                    'total_amount' => $this->total_amount,
                    // 'total_ppn' => $this->total_ppn,
                    'ppn_amount' => $this->total_ppn,
                    'total_discount' => $this->total_discount,
                    'grand_total' => $this->grand_total,
                    'notes' => $this->notes,
                    'status' => $status,
                ];

                if (!$this->isEdit) {
                    $data['created_by'] = Auth::id();
                    $po = PurchaseOrder::create($data);
                } else {
                    $po = PurchaseOrder::findOrFail($this->orderId);
                    $po->update($data);
                    $po->details()->delete();
                }

                foreach ($this->rows as $row) {
                    $po->details()->create([
                        'item_id' => $row['item_id'],
                        'qty_ordered' => $row['qty_ordered'],
                        'purchase_price' => $row['purchase_price'],
                        'discount_percentage' => $row['discount_percentage'],
                        'discount_amount' => $row['discount_amount'],
                        'ppn_percentage' => $row['ppn_percentage'],
                        'ppn_amount' => $row['ppn_amount'],
                        'subtotal' => $row['subtotal'],
                        'notes' => $row['notes'],
                    ]);
                }

                if ($this->purchase_request_id) {
                    PurchaseRequest::find($this->purchase_request_id)->update(['status' => 'closed']);
                }

                if (!empty($this->from_prs)) {
                    PurchaseRequest::whereIn('id', $this->from_prs)->update(['status' => 'closed']);
                }
            });

            session()->flash('notify', ['type' => 'success', 'message' => 'Purchase Order berhasil disimpan.']);
            return redirect()->route('procurement.orders.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorDetails = [];
            foreach ($e->validator->errors()->toArray() as $field => $messages) {
                $errorDetails[] = $field . ': ' . $messages[0];
            }

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Validasi gagal: ' . implode(' | ', $errorDetails)
            ]);
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.procurement.purchase-order-form', [
            'suppliers' => Supplier::where('is_active', true)->get(),
            'warehouses' => Warehouse::where('is_active', true)->get(),
        ]);
    }
}
