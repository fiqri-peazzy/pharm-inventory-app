<?php

namespace App\Livewire\Procurement;

use App\Models\Item;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\Warehouse;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestForm extends Component
{
    public $requestId;
    public $isEdit = false;

    // Header fields
    public $request_number;
    public $warehouse_id;
    public $request_date;
    public $period_month;
    public $period_year;
    public $notes;
    
    // Items table
    public $rows = []; // Each row: {item_id, item_name, item_code, current_stock, average_usage, requested_qty, notes}
    
    // UI states
    public $showItemModal = false;
    public $itemSearch = '';
    public $searchResults = [];

    protected function rules()
    {
        return [
            'warehouse_id' => 'required',
            'request_date' => 'required|date',
            'period_month' => 'required|numeric|min:1|max:12',
            'period_year' => 'required|numeric',
            'rows.*.item_id' => 'required',
            'rows.*.requested_qty' => 'required|numeric|min:1',
        ];
    }

    public function mount($requestId = null)
    {
        $this->request_date = date('Y-m-d');
        $this->period_month = (int)date('m');
        $this->period_year = (int)date('Y');
        
        if ($requestId) {
            $this->requestId = $requestId;
            $this->isEdit = true;
            $this->loadRequest();
        } else {
            $this->generateRequestNumber();
            // Default select first main warehouse if any
            $mainWh = Warehouse::where('is_main', true)->first();
            if ($mainWh) {
                $this->warehouse_id = $mainWh->id;
            }
        }
    }

    public function generateRequestNumber()
    {
        $date = date('Y/m');
        $lastPr = PurchaseRequest::where('request_number', 'like', "PR/$date/%")->latest()->first();
        
        $number = 1;
        if ($lastPr) {
            $lastNum = explode('/', $lastPr->request_number);
            $number = (int)end($lastNum) + 1;
        }
        
        $this->request_number = "PR/$date/" . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function loadRequest()
    {
        $pr = PurchaseRequest::with('details.item')->findOrFail($this->requestId);
        
        if (!in_array($pr->status, ['draft', 'rejected'])) {
            return redirect()->route('procurement.requests.index')->with('error', 'Hanya PR Draft atau Rejected yang dapat diubah.');
        }

        $this->rows = [];

        $this->request_number = $pr->request_number;
        $this->warehouse_id = $pr->warehouse_id;
        $this->request_date = $pr->request_date->format('Y-m-d');
        $this->period_month = $pr->period_month;
        $this->period_year = $pr->period_year;
        $this->notes = $pr->notes;

        foreach ($pr->details as $detail) {
            $this->rows[] = [
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'item_code' => $detail->item->code,
                'current_stock' => $detail->current_stock,
                'average_usage' => $detail->average_usage,
                'requested_qty' => $detail->requested_qty,
                'notes' => $detail->notes,
            ];
        }
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

    public function addItem($itemId)
    {
        // Check if already in rows
        foreach ($this->rows as $row) {
            if ($row['item_id'] == $itemId) {
                $this->dispatch('notify', ['type' => 'warning', 'message' => 'Item sudah ada di daftar.']);
                return;
            }
        }

        $item = Item::findOrFail($itemId);
        
        $this->rows[] = [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'item_code' => $item->code,
            'current_stock' => 0, // In real app, fetch from stock_cards or item_batches
            'average_usage' => 0,
            'requested_qty' => 1,
            'notes' => '',
        ];

        $this->showItemModal = false;
        $this->itemSearch = '';
        $this->searchResults = [];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function save($status = 'draft')
    {
        try {
            $this->validate();

            if (empty($this->rows)) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Minimal harus ada 1 item dalam permintaan.']);
                return;
            }

            \DB::transaction(function () use ($status) {
                $data = [
                    'request_number' => $this->request_number,
                    'warehouse_id' => $this->warehouse_id,
                    'request_date' => $this->request_date,
                    'period_month' => (int)$this->period_month,
                    'period_year' => (int)$this->period_year,
                    'notes' => $this->notes,
                    'status' => $status,
                ];

                if ($status === 'submitted') {
                    $data['submitted_by'] = Auth::id();
                    $data['submitted_at'] = now();
                }

                if (!$this->isEdit) {
                    $data['created_by'] = Auth::id();
                    $pr = PurchaseRequest::create($data);
                } else {
                    $pr = PurchaseRequest::findOrFail($this->requestId);
                    $pr->update($data);
                    $pr->details()->delete(); 
                }

                foreach ($this->rows as $row) {
                    $pr->details()->create([
                        'item_id' => $row['item_id'],
                        'current_stock' => $row['current_stock'] ?? 0,
                        'average_usage' => $row['average_usage'] ?? 0,
                        'requested_qty' => (int)$row['requested_qty'],
                        'notes' => $row['notes'] ?? '',
                    ]);
                }
            });

            $msg = $this->isEdit ? 'dikembangkan' : 'dibuat';
            if ($status === 'submitted') $msg = 'diajukan';
            
            session()->flash('notify', ['type' => 'success', 'message' => "Purchase Request berhasil $msg."]);
            return redirect()->route('procurement.requests.index');

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
            $this->dispatch('notify', [
                'type' => 'error', 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.procurement.purchase-request-form', [
            'warehouses' => Warehouse::all()
        ]);
    }
}
