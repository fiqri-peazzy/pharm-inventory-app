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

    public function createRequest()
    {
        return view('pages.procurement.requests-create');
    }

    public function editRequest($id)
    {
        return view('pages.procurement.requests-edit', compact('id'));
    }

    public function approvals()
    {
        return view('pages.procurement.approvals');
    }

    public function orders()
    {
        return view('pages.procurement.orders');
    }

    public function createOrder()
    {
        return view('pages.procurement.orders-create');
    }

    public function editOrder($id)
    {
        return view('pages.procurement.orders-edit', compact('id'));
    }

    public function print($id)
    {
        $order = PurchaseOrder::with(['supplier', 'warehouse', 'details.item', 'purchaseRequest'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.procurement.po-pdf', compact('order'))
            ->setPaper('a4', 'portrait');
            
        return $pdf->stream('PO-' . str_replace('/', '-', $order->po_number) . '.pdf');
    }
}
