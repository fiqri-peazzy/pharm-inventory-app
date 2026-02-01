<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
class PurchaseController extends Controller
{
    public function prices()
    {
        return view('pages.procurement.item-prices');
    }

    public function requests()
    {
        return view('pages.procurement.requests');
    }

    public function approvals()
    {
        return view('pages.procurement.approvals');
    }

    public function orders()
    {
        return view('pages.procurement.orders');
    }

    public function print($id)
    {
        $order = PurchaseOrder::with(['supplier', 'warehouse', 'details.item'])->findOrFail($id);
        return view('pages.procurement.po-print', compact('order'));
    }
}
