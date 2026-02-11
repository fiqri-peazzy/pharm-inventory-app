<?php

namespace App\Livewire\Clinical;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\Prescription;
use App\Models\ServiceUnit;
use App\Models\Warehouse;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use DB;

class PrescriptionForm extends Component
{
    // Header
    public $prescription_number;
    public $patient_name;
    public $medical_record_number;
    public $service_unit_id;
    public $warehouse_id; // Destination Pharmacy
    public $prescription_date;

    // Items
    public $rows = []; // {item_id, item_name, qty, instruction, available_stock, stock_status}

    // Search
    public $itemSearch = '';
    public $searchResults = [];
    public $showItemModal = false;

    protected $rules = [
        'patient_name' => 'required|string|max:255',
        'service_unit_id' => 'required',
        'warehouse_id' => 'required',
        'prescription_date' => 'required|date',
        'rows.*.item_id' => 'required',
        'rows.*.qty' => 'required|numeric|min:0.1',
    ];

    public function mount()
    {
        $this->prescription_date = date('Y-m-d');
        $this->generateNumber();
        
        // Default warehouse (Pharmacy)
        $pharmacy = Warehouse::where('type', 'depo_farmasi')->first();
        if ($pharmacy) $this->warehouse_id = $pharmacy->id;

        // Default Service Unit
        $unit = ServiceUnit::active()->first();
        if ($unit) $this->service_unit_id = $unit->id;
    }

    public function generateNumber()
    {
        $date = date('Ymd');
        $count = Prescription::whereDate('created_at', date('Y-m-d'))->count() + 1;
        $this->prescription_number = "RX-$date-" . str_pad($count, 4, '0', STR_PAD_LEFT);
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
        
        // Zero-OOS: Check stock in selected pharmacy
        $stock = ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $this->warehouse_id)
            ->where('is_active', true)
            ->where('expired_date', '>', now())
            ->sum('current_qty');

        $status = 'normal';
        if ($stock <= 0) $status = 'out_of_stock';
        elseif ($stock < 20) $status = 'low_stock';

        $this->rows[] = [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'qty' => 1,
            'instruction' => '3 x 1 tablet sesudah makan',
            'available_stock' => $stock,
            'stock_status' => $status
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
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Minimal harus ada 1 obat dalam resep.']);
            return;
        }

        try {
            DB::transaction(function () {
                $prescription = Prescription::create([
                    'prescription_number' => $this->prescription_number,
                    'patient_name' => $this->patient_name,
                    'medical_record_number' => $this->medical_record_number,
                    'doctor_id' => Auth::id(),
                    'doctor_name' => Auth::user()->name,
                    'service_unit_id' => $this->service_unit_id,
                    'warehouse_id' => $this->warehouse_id,
                    'prescription_date' => $this->prescription_date,
                    'status' => 'submitted',
                ]);

                foreach ($this->rows as $row) {
                    $prescription->details()->create([
                        'item_id' => $row['item_id'],
                        'qty' => $row['qty'],
                        'instruction' => $row['instruction'],
                    ]);
                }
            });

            session()->flash('notify', ['type' => 'success', 'message' => 'Resep berhasil dikirim ke Apotek.']);
            return redirect()->route('clinical.prescriptions.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan resep: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.clinical.prescription-form', [
            'serviceUnits' => ServiceUnit::active()->get(),
            'pharmacies' => Warehouse::whereIn('type', ['depo_farmasi', 'depo_igd', 'depo_ok'])->get()
        ]);
    }
}
