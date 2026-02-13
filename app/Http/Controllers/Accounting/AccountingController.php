<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function journals()
    {
        return view('pages.accounting.journals.index');
    }

    public function createJournal()
    {
        return view('pages.accounting.journals.create');
    }

    public function editJournal($id)
    {
        return view('pages.accounting.journals.edit', compact('id'));
    }

    public function showJournal($id)
    {
        return view('pages.accounting.journals.show', compact('id'));
    }

    public function coa()
    {
        // For later: Chart of Accounts management
        return view('pages.accounting.coa.index');
    }

    public function generalLedger()
    {
        return view('pages.accounting.reports.general-ledger');
    }

    public function trialBalance()
    {
        return view('pages.accounting.reports.trial-balance');
    }
}
