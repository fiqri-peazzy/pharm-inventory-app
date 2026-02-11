<?php

namespace App\Livewire\Clinical;

use App\Models\WardRequest;
use App\Models\Warehouse;
use App\Models\ServiceUnit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class WardRequestIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $warehouse_id = ''; // Source Pharmacy

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = WardRequest::with(['serviceUnit', 'warehouse', 'requestedBy'])
            ->when($this->search, function ($q) {
                $q->where('request_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->warehouse_id, fn($q) => $q->where('warehouse_id', $this->warehouse_id))
            ->latest();

        return view('livewire.clinical.ward-request-index', [
            'requests' => $query->paginate(10),
            'pharmacies' => Warehouse::whereIn('type', ['gudang_utama', 'depo_farmasi', 'depo_igd'])->get()
        ]);
    }
}
