<?php

namespace App\Livewire\Accounting\Reports;

use App\Models\Account;
use App\Models\JournalEntryDetail;
use Livewire\Component;

class TrialBalance extends Component
{
    public $dateTo;

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $accounts = Account::where('is_active', true)
            ->with(['journalEntryDetails' => function ($q) {
                $q->whereHas('journalEntry', function ($sq) {
                    $sq->where('status', 'posted')
                        ->whereDate('journal_date', '<=', $this->dateTo);
                });
            }])
            ->orderBy('code')
            ->get();

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $sumDebit = $account->journalEntryDetails->sum('debit');
            $sumCredit = $account->journalEntryDetails->sum('credit');
            $netDebit = $sumDebit - $sumCredit;

            if ($netDebit != 0) {
                if ($netDebit > 0) {
                    $rows[] = [
                        'code' => $account->code,
                        'name' => $account->name,
                        'debit' => abs($netDebit),
                        'credit' => 0,
                    ];
                    $totalDebit += abs($netDebit);
                } else {
                    $rows[] = [
                        'code' => $account->code,
                        'name' => $account->name,
                        'debit' => 0,
                        'credit' => abs($netDebit),
                    ];
                    $totalCredit += abs($netDebit);
                }
            }
        }

        return view('livewire.accounting.reports.trial-balance', [
            'rows' => $rows,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'isBalanced' => round($totalDebit, 2) === round($totalCredit, 2)
        ]);
    }
}
