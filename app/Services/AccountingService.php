<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AccountingService
{
    /**
     * Create a new journal entry with details.
     *
     * @param array $data
     * @return JournalEntry
     * @throws Exception
     */
    public function createJournalEntry(array $data)
    {
        return DB::transaction(function () use ($data) {
            $totalDebit = collect($data['entries'])->sum('debit');
            $totalCredit = collect($data['entries'])->sum('credit');

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new Exception("Journal entry is not balanced. Debit: $totalDebit, Credit: $totalCredit");
            }

            if (count($data['entries']) < 2) {
                throw new Exception("Journal entry must have at least 2 lines.");
            }

            $journal = JournalEntry::create([
                'journal_number' => $data['journal_number'] ?? $this->generateJournalNumber($data['type'] ?? 'standard'),
                'journal_date' => $data['journal_date'] ?? now(),
                'type' => $data['type'] ?? 'standard',
                'transaction_type' => $data['transaction_type'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'created_by' => Auth::id() ?? 1, // Default to admin for auto-posting
            ]);

            foreach ($data['entries'] as $entry) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit'],
                    'description' => $entry['description'] ?? null,
                ]);
            }

            return $journal;
        });
    }

    /**
     * Generate a unique journal number.
     *
     * @param string $type
     * @return string
     */
    public function generateJournalNumber(string $type)
    {
        $prefix = match ($type) {
            'adjusting' => 'ADJ',
            'closing' => 'CLS',
            'opening' => 'OPN',
            default => 'JE',
        };

        $year = date('Y');
        $count = JournalEntry::whereYear('created_at', $year)->count() + 1;
        
        return sprintf("%s/%s/%04d", $prefix, $year, $count);
    }

    /**
     * Get account by code.
     *
     * @param string $code
     * @return Account|null
     */
    public function getAccountByCode(string $code)
    {
        return Account::where('code', $code)->where('is_active', true)->first();
    }

    /**
     * Post a journal entry (lock and update balances if needed).
     *
     * @param JournalEntry $journal
     * @return void
     */
    public function postJournal(JournalEntry $journal)
    {
        if ($journal->status === 'posted') {
            return;
        }

        $journal->update([
            'status' => 'posted',
            'posted_by' => Auth::id() ?? 1,
            'posted_at' => now(),
        ]);
    }

    /**
     *
     * @param string|null $categoryType
     * @return Account
     */
    public function getInventoryAccountByCategory(?string $categoryType)
    {
        $code = match ($categoryType) {
            'obat' => '11100',
            'alkes' => '11200',
            'bmhp' => '11200', // Assuming BMHP is also supplies
            'reagensia' => '11300',
            default => '11900',
        };

        return $this->getAccountByCode($code) ?? $this->getAccountByCode('11900');
    }

    /**
     * Map item category code/type to COGS Account.
     *
     * @param string|null $categoryType
     * @return Account
     */
    public function getCOGSAccountByCategory(?string $categoryType)
    {
        $code = match ($categoryType) {
            'obat' => '51100',
            'alkes' => '51200',
            'bmhp' => '51200',
            default => '51900',
        };

        return $this->getAccountByCode($code) ?? $this->getAccountByCode('51900');
    }

    /**
     * Get AP Account for a supplier.
     *
     * @param mixed $supplier
     * @return Account
     */
    public function getAPAccountBySupplier($supplier = null)
    {
        return $this->getAccountByCode('21100');
    }

    /**
     * Get Adjustment Income Account.
     *
     * @return Account
     */
    public function getAdjustmentIncomeAccount()
    {
        return $this->getAccountByCode('49100');
    }

    /**
     * Get Adjustment Expense Account.
     *
     * @return Account
     */
    public function getAdjustmentExpenseAccount()
    {
        return $this->getAccountByCode('52900');
    }

    /**
     * Get Disposal Loss Account.
     *
     * @return Account
     */
    public function getDisposalLossAccount()
    {
        return $this->getAccountByCode('52200');
    }

    /**
     * Get Stock Loss Account.
     *
     * @return Account
     */
    public function getStockLossAccount()
    {
        return $this->getAccountByCode('52100');
    }

    /**
     * Get Shrinkage/Opname Loss Account.
     *
     * @return Account
     */
    public function getShrinkageAccount()
    {
        return $this->getAccountByCode('52300');
    }

    /**
     * Approve and post a draft journal entry.
     */
    public function approve(JournalEntry $journal)
    {
        if ($journal->status !== 'draft') {
            throw new \Exception("Journal is already " . $journal->status);
        }

        return $this->postJournal($journal);
    }
}
