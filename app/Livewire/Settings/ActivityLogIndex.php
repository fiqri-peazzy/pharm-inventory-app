<?php

namespace App\Livewire\Settings;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $moduleFilter = '';
    public $actionFilter = '';
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->subDays(7)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $logs = ActivityLog::with('user')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('record_id', 'like', '%' . $this->search . '%');
            })
            ->when($this->moduleFilter, fn($q) => $q->byModule($this->moduleFilter))
            ->when($this->actionFilter, fn($q) => $q->byAction($this->actionFilter))
            ->whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('livewire.settings.activity-log-index', [
            'logs' => $logs,
            'modules' => ActivityLog::select('module')->distinct()->pluck('module'),
            'actions' => ActivityLog::select('action')->distinct()->pluck('action'),
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->moduleFilter = '';
        $this->actionFilter = '';
        $this->mount();
    }
}
