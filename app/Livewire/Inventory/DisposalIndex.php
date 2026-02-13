<?php

namespace App\Livewire\Inventory;

use App\Models\Disposal;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DisposalIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $status_filter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('do-delete-disposal')]
    public function deleteDisposal($id)
    {
        if (!auth()->user()->can('disposals.delete')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $disposal = Disposal::findOrFail($id);

        if ($disposal->status === 'posted') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Dokumen posted tidak dapat dihapus.']);
            return;
        }

        $disposal->details()->delete();
        $disposal->delete();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Dokumen berhasil dihapus.']);
    }

    public function render()
    {
        $query = Disposal::with(['warehouse', 'creator'])
            ->when($this->search, function ($q) {
                $q->where('disposal_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->type_filter, function ($q) {
                $q->where('type', $this->type_filter);
            })
            ->when($this->status_filter, function ($q) {
                $q->where('status', $this->status_filter);
            })
            ->latest();

        $stats = [
            'total' => Disposal::count(),
            'draft' => Disposal::where('status', 'draft')->count(),
            'submitted' => Disposal::where('status', 'submitted')->count(),
            'approved' => Disposal::where('status', 'approved')->count(),
            'executed' => Disposal::where('status', 'executed')->count(),
            'posted' => Disposal::where('status', 'posted')->count(),
        ];

        return view('livewire.inventory.disposal-index', [
            'disposals' => $query->paginate(10),
            'stats' => $stats
        ]);
    }
}
