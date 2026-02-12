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

    // Modal states
    public $showDetailModal = false;
    public $showEtiketModal = false;
    public $showPrintModal = false; // New modal for Salinan Resep
    public $selectedPrescription = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function printEtiket($id)
    {
        $this->selectedPrescription = Prescription::with(['details.item', 'serviceUnit'])->findOrFail($id);
        $this->showEtiketModal = true;
    }

    public function printPrescription($id)
    {
        $this->selectedPrescription = Prescription::with(['details.item', 'warehouse', 'doctor', 'serviceUnit'])->findOrFail($id);
        $this->showPrintModal = true;
    }

    public function viewDetails($id)
    {
        $this->selectedPrescription = Prescription::with(['details.item', 'warehouse', 'doctor', 'serviceUnit'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeModals()
    {
        $this->showDetailModal = false;
        $this->showEtiketModal = false;
        $this->showPrintModal = false;
        $this->selectedPrescription = null;
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
