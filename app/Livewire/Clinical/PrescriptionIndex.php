<?php

namespace App\Livewire\Clinical;

use App\Models\Prescription;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class PrescriptionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $warehouse_id = '';
    public $service_unit_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Prescription::with(['doctor', 'warehouse'])
            ->when($this->search, function ($q) {
                $q->where('prescription_number', 'like', '%' . $this->search . '%')
                  ->orWhere('patient_name', 'like', '%' . $this->search . '%')
                  ->orWhere('medical_record_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->warehouse_id, fn($q) => $q->where('warehouse_id', $this->warehouse_id))
            ->when($this->service_unit_id, fn($q) => $q->where('service_unit_id', $this->service_unit_id))
            ->latest();

        return view('livewire.clinical.prescription-index', [
            'prescriptions' => $query->paginate(10),
            'pharmacies' => Warehouse::whereIn('type', ['depo_farmasi', 'depo_igd', 'depo_ok'])->get(),
            'serviceUnits' => \App\Models\ServiceUnit::active()->get()
        ]);
    }
}
