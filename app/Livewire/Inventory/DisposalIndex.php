<?php

namespace App\Livewire\Inventory;

use App\Models\Disposal;
use Livewire\Component;
use Livewire\WithPagination;

class DisposalIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $status = '';

    protected $queryString = ['search', 'type', 'status'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $disposal = Disposal::findOrFail($id);
        if ($disposal->status !== 'draft') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya draft yang dapat dihapus.']);
            return;
        }
        $disposal->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data disposal berhasil dihapus.']);
    }

    public function render()
    {
        $query = Disposal::with(['warehouse', 'creator'])
            ->latest();

        if ($this->search) {
            $query->where('disposal_number', 'like', '%' . $this->search . '%');
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.inventory.disposal-index', [
            'disposals' => $query->paginate(10)
        ]);
    }
}
