<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use DB;

class JournalForm extends Component
{
    public $journalId;
    public $isEdit = false;
    public $isViewOnly = false;

    // Header Data
    public $journal_number;
    public $journal_date;
    public $type = 'standard';
    public $reference;
    public $description;
    public $status = 'draft';

    // Entries
    public $entries = []; // {account_id, debit, credit, description}

    // Totals
    public $total_debit = 0;
    public $total_credit = 0;
    public $difference = 0;

    protected function rules()
    {
        return [
            'journal_date' => 'required|date',
            'type' => 'required|in:standard,adjusting,closing',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.debit' => 'numeric|min:0',
            'entries.*.credit' => 'numeric|min:0',
        ];
    }

    public function mount($journalId = null)
    {
        $this->journal_date = now()->format('Y-m-d');
        
        if (request()->query('view') == 1) {
            $this->isViewOnly = true;
        }

        if ($journalId) {
            $this->journalId = $journalId;
            $this->isEdit = true;
            $this->loadJournal();
        } else {
            $this->addEntry();
            $this->addEntry();
        }
    }

    public function loadJournal()
    {
        $journal = JournalEntry::with('details.account')->findOrFail($this->journalId);
        
        $this->journal_number = $journal->journal_number;
        $this->journal_date = $journal->journal_date->format('Y-m-d');
        $this->type = $journal->type;
        $this->reference = $journal->reference;
        $this->description = $journal->description;
        $this->status = $journal->status;

        if ($this->status !== 'draft') {
            $this->isViewOnly = true;
        }

        foreach ($journal->details as $detail) {
            $this->entries[] = [
                'id' => $detail->id,
                'account_id' => $detail->account_id,
                'debit' => $detail->debit,
                'credit' => $detail->credit,
                'description' => $detail->description,
            ];
        }

        $this->calculateTotals();
    }

    public function addEntry()
    {
        $this->entries[] = [
            'account_id' => '',
            'debit' => 0,
            'credit' => 0,
            'description' => $this->description,
        ];
    }

    public function removeEntry($index)
    {
        if (count($this->entries) > 2) {
            unset($this->entries[$index]);
            $this->entries = array_values($this->entries);
            $this->calculateTotals();
        }
    }

    public function updatedEntries($value, $key)
    {
        if (str_contains($key, 'debit') || str_contains($key, 'credit')) {
            $this->calculateTotals();
        }
    }

    public function calculateTotals()
    {
        $this->total_debit = collect($this->entries)->sum(fn($e) => (float)($e['debit'] ?: 0));
        $this->total_credit = collect($this->entries)->sum(fn($e) => (float)($e['credit'] ?: 0));
        $this->difference = round($this->total_debit - $this->total_credit, 2);
    }

    public function save($status = 'draft')
    {
        $this->validate();

        if ($this->difference != 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Jurnal tidak seimbang (Total Debit harus sama dengan Total Kredit). Selisih: ' . number_format($this->difference)]);
            return;
        }

        try {
            DB::transaction(function () use ($status) {
                $accountingService = app(AccountingService::class);
                
                $data = [
                    'journal_date' => $this->journal_date,
                    'type' => $this->type,
                    'reference' => $this->reference,
                    'description' => $this->description,
                    'status' => $status,
                    'entries' => $this->entries,
                ];

                if ($this->isEdit) {
                    $journal = JournalEntry::findOrFail($this->journalId);
                    $journal->update([
                        'journal_date' => $data['journal_date'],
                        'type' => $data['type'],
                        'reference' => $data['reference'],
                        'description' => $data['description'],
                        'status' => $status,
                        'total_debit' => $this->total_debit,
                        'total_credit' => $this->total_credit,
                    ]);

                    $journal->details()->delete();
                    foreach ($this->entries as $entry) {
                        $journal->details()->create([
                            'account_id' => $entry['account_id'],
                            'debit' => $entry['debit'] ?: 0,
                            'credit' => $entry['credit'] ?: 0,
                            'description' => $entry['description'],
                        ]);
                    }
                } else {
                    $accountingService->createJournalEntry($data);
                }
            });

            session()->flash('notify', ['type' => 'success', 'message' => 'Jurnal berhasil disimpan.']);
            return redirect()->route('accounting.journals.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan jurnal: ' . $e->getMessage()]);
        }
    }

    public function post()
    {
        if (!$this->isEdit) return;

        try {
            $journal = JournalEntry::findOrFail($this->journalId);
            app(AccountingService::class)->postJournal($journal);
            
            session()->flash('notify', ['type' => 'success', 'message' => 'Jurnal berhasil di-posting.']);
            return redirect()->route('accounting.journals.index');
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal posting jurnal: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.accounting.journal-form', [
            'accounts' => Account::where('is_active', true)->orderBy('code')->get()
        ]);
    }
}
