<?php

namespace App\Livewire\Procurement;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\PurchaseOrder;
use App\Models\Receiving;
use App\Models\ReceivingDetail;
use App\Models\StockCard;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReceivingForm extends Component
{
    use WithFileUploads;

    public $receivingId;
    public $receiving_number;
    public $purchase_order_id;
    public $is_triangulated = false; // "Fisik Sesuai Faktur & SP" checkbox
    public $supplier_id;
    public $warehouse_id;
    public $receiving_date;
    public $invoice_number;
    public $invoice_date;
    public $invoice_file;
    public $invoice_file_path;
    public $notes;
    public $status = 'draft';

    // Totals
    public $total_amount = 0;
    public $ppn_amount = 0;
    public $grand_total = 0;

    public $rows = [];
    public $showItemModal = false;
    public $itemSearch = '';
    public $searchResults = [];

    protected function rules()
    {
        return [
            'receiving_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|date',
            'invoice_file' => 'nullable|image|max:2048', // Max 2MB
            'purchase_order_id' => 'required',
            'is_triangulated' => 'accepted', // Must be checked to proceed
            'rows.*.item_id' => 'required|exists:items,id',
            'rows.*.qty_received' => 'required|numeric|min:0.01',
            'rows.*.batch_number' => 'required|string',
            'rows.*.expired_date' => 'required|date|after:today', // Must be a future date
            'rows.*.purchase_price' => 'required|numeric|min:0.01',
        ];
    }

    public function mount($receivingId = null)
    {
        $this->receiving_date = date('Y-m-d');
        $this->invoice_date = date('Y-m-d');

        if ($receivingId) {
            $this->loadReceiving($receivingId);
        } else {
            $this->generateReceivingNumber();
            $this->addRow();
        }
    }

    public function generateReceivingNumber()
    {
        $prefix = 'RCV/' . date('Y/m');
        $last = Receiving::where('receiving_number', 'like', $prefix . '%')
            ->latest()
            ->first();

        if ($last) {
            $lastNumber = intval(substr($last->receiving_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $this->receiving_number = $prefix . '/' . $newNumber;
    }

    public function loadReceiving($id)
    {
        $rcv = Receiving::with('details.item')->findOrFail($id);
        $this->receivingId = $rcv->id;
        $this->receiving_number = $rcv->receiving_number;
        $this->purchase_order_id = $rcv->purchase_order_id;
        $this->supplier_id = $rcv->supplier_id;
        $this->warehouse_id = $rcv->warehouse_id;
        $this->receiving_date = $rcv->receiving_date->format('Y-m-d');
        $this->invoice_number = $rcv->invoice_number;
        $this->invoice_date = $rcv->invoice_date->format('Y-m-d');
        $this->invoice_file_path = $rcv->invoice_file;
        $this->notes = $rcv->notes;
        $this->status = $rcv->status;

        $this->rows = [];
        foreach ($rcv->details as $detail) {
            $this->rows[] = [
                'id' => $detail->id,
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'item_code' => $detail->item->code,
                'qty_ordered' => 0, // Will be updated if PO selected
                'qty_remaining' => 0, // Will be updated if PO selected
                'qty_received' => $detail->qty_received,
                'batch_number' => $detail->batch_number,
                'expired_date' => $detail->expired_date->format('Y-m-d'),
                'purchase_price' => (float)$detail->purchase_price,
                'discount_percentage' => (float)($detail->discount_percentage ?? 0),
                'discount_amount' => (float)($detail->discount_amount ?? 0),
                'ppn_percentage' => (float)$detail->ppn_percentage,
                'ppn_amount' => (float)$detail->ppn_amount,
                'subtotal' => (float)$detail->subtotal,
            ];
        }
        $this->calculateTotals();
    }

    public function updatedPurchaseOrderId($value)
    {
        if ($value) {
            $po = PurchaseOrder::with('details.item')->find($value);
            if ($po) {
                $this->supplier_id = $po->supplier_id;
                $this->warehouse_id = $po->warehouse_id;
                $this->rows = [];
                
                foreach ($po->details as $detail) {
                    // Calculate remaining to receive
                    $alreadyReceived = ReceivingDetail::where('item_id', $detail->item_id)
                        ->whereHas('receiving', function($q) use ($po) {
                            $q->where('purchase_order_id', $po->id)->where('status', 'posted');
                        })->sum('qty_received');

                    $remaining = $detail->qty_ordered - $alreadyReceived;

                    if ($remaining > 0) {
                        $this->rows[] = [
                            'item_id' => $detail->item_id,
                            'item_name' => $detail->item->name,
                            'item_code' => $detail->item->code,
                            'qty_ordered' => $detail->qty_ordered,
                            'qty_remaining' => $remaining,
                            'qty_received' => $remaining,
                            'batch_number' => '',
                            'expired_date' => '',
                            'purchase_price' => $detail->purchase_price,
                            'discount_percentage' => $detail->discount_percentage ?? 0,
                            'discount_amount' => 0,
                            'ppn_percentage' => $detail->ppn_percentage ?? 11,
                            'ppn_amount' => 0,
                            'subtotal' => 0,
                        ];
                    }
                }
                $this->calculateTotals();
            }
        }
    }

    public function addRow()
    {
        $this->rows[] = [
            'item_id' => '',
            'item_name' => '',
            'item_code' => '',
            'qty_ordered' => 0,
            'qty_remaining' => 0,
            'qty_received' => 1,
            'batch_number' => '',
            'expired_date' => '',
            'purchase_price' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'ppn_percentage' => 11,
            'ppn_amount' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->total_amount = 0;
        $this->ppn_amount = 0;

        foreach ($this->rows as $index => $row) {
            $qty = floatval($row['qty_received']);
            $price = floatval($row['purchase_price']);
            $discountPercentage = floatval($row['discount_percentage'] ?? 0);
            
            // Calculate discount amount
            $grossAmount = $qty * $price;
            $discountAmount = $grossAmount * ($discountPercentage / 100);
            $netAmount = $grossAmount - $discountAmount;
            
            // Calculate PPN on net amount (after discount)
            $ppn = $netAmount * (floatval($row['ppn_percentage'] ?? 0) / 100);
            
            // Update row calculations
            $this->rows[$index]['discount_amount'] = $discountAmount;
            $this->rows[$index]['ppn_amount'] = $ppn;
            $this->rows[$index]['subtotal'] = $netAmount + $ppn;

            $this->total_amount += $netAmount;
            $this->ppn_amount += $ppn;
        }

        $this->grand_total = $this->total_amount + $this->ppn_amount;
    }

    public function updatedRows()
    {
        $this->calculateTotals();
    }

    public function updated($name)
    {
        if (preg_match('/^rows\.(\d+)\.batch_number$/', $name, $m)) {
            $this->syncBatchInfo((int) $m[1]);
        }
    }

    /**
     * When staff types a batch number that already exists for this item,
     * auto-fill the expiry date from the known record (or warn if what
     * they typed conflicts with it) so a typo doesn't fragment FEFO
     * tracking into two "different" batches that are really the same one.
     */
    protected function syncBatchInfo($index)
    {
        $row = $this->rows[$index] ?? null;
        if (!$row || empty($row['item_id']) || empty($row['batch_number'])) {
            return;
        }

        $existing = ItemBatch::where('item_id', $row['item_id'])
            ->where('batch_number', $row['batch_number'])
            ->first();

        if (!$existing) {
            return;
        }

        $knownDate = $existing->expired_date->format('Y-m-d');

        if (empty($row['expired_date'])) {
            $this->rows[$index]['expired_date'] = $knownDate;
        } elseif ($row['expired_date'] !== $knownDate) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Nomor batch \"{$row['batch_number']}\" untuk item ini sudah pernah tercatat dengan ED " . $existing->expired_date->format('d/m/Y') . ". Pastikan ED yang diinput sesuai kemasan fisik.",
            ]);
        }
    }

    public function existingBatchNumbers($itemId)
    {
        if (!$itemId) {
            return [];
        }

        return ItemBatch::where('item_id', $itemId)
            ->select('batch_number')
            ->distinct()
            ->limit(10)
            ->pluck('batch_number');
    }

    public function save($status = 'draft')
    {
        $this->validate();

        // Enforcement: Receiving from PBF must be to Main Warehouse
        $warehouse = Warehouse::find($this->warehouse_id);
        if (!$warehouse || !$warehouse->is_main) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Penerimaan barang dari PBF hanya diperbolehkan ke Gudang Utama.']);
            return;
        }

        if ($this->grand_total <= 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Total penerimaan tidak boleh nol.']);
            return;
        }

        try {
            DB::transaction(function () use ($status) {
                $rcv = Receiving::updateOrCreate(
                    ['id' => $this->receivingId],
                    [
                        'receiving_number' => $this->receiving_number,
                        'purchase_order_id' => $this->purchase_order_id ?: null,
                        'supplier_id' => $this->supplier_id,
                        'warehouse_id' => $this->warehouse_id,
                        'receiving_date' => $this->receiving_date,
                        'invoice_number' => $this->invoice_number,
                        'invoice_date' => $this->invoice_date,
                        'total_amount' => $this->total_amount,
                        'ppn_amount' => $this->ppn_amount,
                        'grand_total' => $this->grand_total,
                        'notes' => $this->notes,
                        'status' => ($status === 'posted') ? 'approved' : (($status === 'approved') ? 'approved' : 'draft'),
                        'created_by' => auth()->id(),
                    ]
                );

                if ($this->invoice_file) {
                    $fileName = 'invoice_' . time() . '.' . $this->invoice_file->getClientOriginalExtension();
                    $path = $this->invoice_file->storeAs('invoices', $fileName, 'public');
                    $rcv->update(['invoice_file' => $path]);
                }

                $rcv->details()->delete();
                foreach ($this->rows as $row) {
                    $rcv->details()->create($row);
                }

                if ($status === 'posted') {
                    app(\App\Services\Inventory\ReceivingService::class)->post($rcv);
                }
            });

            $msg = $status === 'posted' ? 'Penerimaan berhasil diposting dan stok telah diupdate.' : 'Draft penerimaan berhasil disimpan.';
            session()->flash('success', $msg);
            return redirect()->route('procurement.receivings.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function updatedItemSearch($value)
    {
        if (strlen($value) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Item::where('name', 'like', '%' . $value . '%')
            ->orWhere('code', 'like', '%' . $value . '%')
            ->limit(5)
            ->get();
    }

    public function selectItem($itemId)
    {
        $item = Item::find($itemId);
        if ($item) {
            // Add to a new row or empty row
            $added = false;
            foreach ($this->rows as $index => $row) {
                if (empty($row['item_id'])) {
                    $this->rows[$index]['item_id'] = $item->id;
                    $this->rows[$index]['item_name'] = $item->name;
                    $this->rows[$index]['item_code'] = $item->code;
                    $added = true;
                    break;
                }
            }

            if (!$added) {
                $this->rows[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'item_code' => $item->code,
                    'qty_ordered' => 0,
                    'qty_remaining' => 0,
                    'qty_received' => 1,
                    'batch_number' => '',
                    'expired_date' => '',
                    'purchase_price' => 0,
                    'discount_percentage' => 0,
                    'discount_amount' => 0,
                    'ppn_percentage' => 11,
                    'ppn_amount' => 0,
                    'subtotal' => 0,
                ];
            }
        }
        $this->dispatch('close-item-modal');
        $this->itemSearch = '';
        $this->searchResults = [];
        $this->calculateTotals();
    }

    public function render()
    {
        return view('livewire.procurement.receiving-form', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_main', true)->orderBy('name')->get(),
            'purchaseOrders' => PurchaseOrder::whereIn('status', ['approved', 'sent', 'partial_received'])->latest()->get(),
        ]);
    }
}
