<?php

namespace App\Livewire\Accounting;

use App\Models\JournalEntry;
use Livewire\Component;
use Livewire\WithPagination;

class JournalIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $status = '';
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $query = JournalEntry::with(['creator', 'approver'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('journal_number', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('journal_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('journal_date', '<=', $this->dateTo))
            ->orderBy('journal_date', 'desc')
            ->orderBy('created_at', 'desc');

        return view('livewire.accounting.journal-index', [
            'journals' => $query->paginate(15)
        ]);
    }
}
