<?php

namespace App\Livewire\Procurement;

use App\Models\Receiving;
use Livewire\Component;
use Livewire\WithPagination;

class ReceivingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $dateFrom;
    public $dateTo;

    protected $queryString = ['search', 'status', 'dateFrom', 'dateTo'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Receiving::with(['supplier', 'warehouse', 'purchaseOrder', 'creator'])
            ->latest('receiving_date');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('receiving_number', 'like', '%' . $this->search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', function($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->dateFrom) {
            $query->whereDate('receiving_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('receiving_date', '<=', $this->dateTo);
        }

        return view('livewire.procurement.receiving-index', [
            'receivings' => $query->paginate(10)
        ]);
    }
}
