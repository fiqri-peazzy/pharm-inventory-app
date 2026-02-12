<?php

namespace App\Livewire\Clinical;

use App\Models\Item;
use App\Models\WardRequest;
use App\Models\ServiceUnit;
use App\Models\Warehouse;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use DB;

class WardRequestForm extends Component
{
    public $requestId;
    public $isEdit = false;
    // Header
    public $request_number;
    public $service_unit_id;
    public $warehouse_id; // Source Pharmacy
    public $request_date;
    public $notes;

    // Items
    public $rows = []; // {item_id, item_name, qty_requested, notes}

    // Search
    public $itemSearch = '';
    public $searchResults = [];
    public $showItemModal = false;

    protected $rules = [
        'service_unit_id' => 'required',
        'warehouse_id' => 'required',
        'request_date' => 'required|date',
        'rows.*.item_id' => 'required',
        'rows.*.qty_requested' => 'required|numeric|min:1',
    ];

    public function mount($requestId = null)
    {
        $this->request_date = date('Y-m-d');
        
        if ($requestId) {
            $this->requestId = $requestId;
            $this->isEdit = true;
            $request = WardRequest::with('details.item')->findOrFail($requestId);
            
            $this->request_number = $request->request_number;
            $this->service_unit_id = $request->service_unit_id;
            $this->warehouse_id = $request->warehouse_id;
            $this->request_date = $request->request_date->format('Y-m-d');
            $this->notes = $request->notes;
            
            foreach ($request->details as $detail) {
                $this->rows[] = [
                    'item_id' => $detail->item_id,
                    'item_name' => $detail->item->name,
                    'qty_requested' => $detail->qty_requested,
                    'notes' => $detail->notes
                ];
            }
        } else {
            $this->generateNumber();

            // Try to pre-select unit based on user (optional logic)
            $unit = ServiceUnit::active()->first();
            if ($unit) {
                $this->service_unit_id = $unit->id;
                if ($unit->default_warehouse_id) {
                    $this->warehouse_id = $unit->default_warehouse_id;
                }
            }

            if (!$this->warehouse_id) {
                $mainWh = Warehouse::where('is_main', true)->first();
                if ($mainWh) $this->warehouse_id = $mainWh->id;
            }
        }
    }

    public function generateNumber()
    {
        $date = date('Y/m');
        $count = WardRequest::where('request_number', 'like', "REQ/$date/%")->count() + 1;
        $this->request_number = "REQ/$date/" . str_pad($count, 4, '0', STR_PAD_LEFT);
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

    public function selectItem($itemId)
    {
        $item = Item::findOrFail($itemId);
        
        $this->rows[] = [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'qty_requested' => 1,
            'notes' => ''
        ];

        $this->dispatch('close-item-modal');
        $this->itemSearch = '';
        $this->searchResults = [];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function save()
    {
        $this->validate();

        if (empty($this->rows)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Minimal harus ada 1 item yang diminta.']);
            return;
        }

        try {
            DB::transaction(function () {
                $data = [
                    'request_number' => $this->request_number,
                    'service_unit_id' => $this->service_unit_id,
                    'warehouse_id' => $this->warehouse_id,
                    'request_date' => $this->request_date,
                    'status' => 'requested',
                    'notes' => $this->notes,
                    'requested_by' => Auth::id(),
                ];

                if ($this->isEdit) {
                    $request = WardRequest::findOrFail($this->requestId);
                    $request->update($data);
                    $request->details()->delete();
                } else {
                    $request = WardRequest::create($data);
                }

                foreach ($this->rows as $row) {
                    $request->details()->create([
                        'item_id' => $row['item_id'],
                        'qty_requested' => $row['qty_requested'],
                        'notes' => $row['notes'],
                    ]);
                }
            });

            session()->flash('notify', ['type' => 'success', 'message' => $this->isEdit ? 'Permintaan unit berhasil diperbarui.' : 'Permintaan unit berhasil dikirim.']);
            return redirect()->route('clinical.ward-requests.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan permintaan: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.clinical.ward-request-form', [
            'serviceUnits' => ServiceUnit::active()->get(),
            'pharmacies' => Warehouse::whereIn('type', ['gudang_utama', 'depo_farmasi', 'depo_igd'])->get()
        ]);
    }
}
