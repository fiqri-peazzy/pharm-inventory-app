<?php

namespace App\Livewire\Accounting\Reports;

use App\Models\Account;
use App\Models\JournalEntryDetail;
use Livewire\Component;

class GeneralLedger extends Component
{
    public $accountId;
    public $dateFrom;
    public $dateTo;
    
    public $openingBalance = 0;
    public $runningBalance = 0;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $account = null;
        $items = collect();

        if ($this->accountId) {
            $account = Account::findOrFail($this->accountId);
            
            // 1. Calculate Opening Balance (before dateFrom)
            $preDetails = JournalEntryDetail::where('account_id', $this->accountId)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted')
                        ->whereDate('journal_date', '<', $this->dateFrom);
                })->get();

            $this->openingBalance = 0;
            foreach ($preDetails as $detail) {
                if ($account->normal_balance === 'debit') {
                    $this->openingBalance += ($detail->debit - $detail->credit);
                } else {
                    $this->openingBalance += ($detail->credit - $detail->debit);
                }
            }

            // 2. Fetch Transactions in range
            $items = JournalEntryDetail::with('journalEntry')
                ->where('account_id', $this->accountId)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted')
                        ->whereBetween('journal_date', [$this->dateFrom, $this->dateTo]);
                })
                ->get()
                ->sortBy(fn($d) => $d->journalEntry->journal_date->timestamp . '_' . $d->journalEntry->created_at->timestamp);
        }

        return view('livewire.accounting.reports.general-ledger', [
            'accounts' => Account::where('is_active', true)->orderBy('code')->get(),
            'account' => $account,
            'items' => $items
        ]);
    }
}
