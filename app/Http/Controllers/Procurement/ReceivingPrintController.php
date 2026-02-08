<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Receiving;
use Illuminate\Http\Request;

class ReceivingPrintController extends Controller
{
    public function print($id)
    {
        $receiving = Receiving::with(['supplier', 'warehouse', 'purchaseOrder', 'details.item', 'creator', 'approver'])
            ->findOrFail($id);

        return view('pdf.receiving-ba', compact('receiving'));
    }
}
