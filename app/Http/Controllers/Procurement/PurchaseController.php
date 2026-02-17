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

    public function rko()
    {
        return view('pages.procurement.rko');
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
    // pdf dompdf implementation test push
    public function print($id)
    {
        $order = PurchaseOrder::with(['supplier', 'warehouse', 'details.item', 'purchaseRequest'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.procurement.po-pdf', compact('order'))
            ->setPaper('a4', 'portrait');
            
        return $pdf->stream('PO-' . str_replace('/', '-', $order->po_number) . '.pdf');
    }

    public function printRko(Request $request)
    {
        $warehouseId = $request->get('warehouse_id', 'all');
        $projectionDays = $request->get('projection_days', 30);
        $search = $request->get('search', '');
        $ven = $request->get('ven', '');
        $abc = $request->get('abc', '');

        $service = new \App\Services\Procurement\RkoService();
        
        if ($warehouseId === 'all') {
            $suggestions = $service->calculateGlobalRko($projectionDays);
        } else {
            $warehouse = \App\Models\Warehouse::find($warehouseId);
            $suggestions = $warehouse ? $service->calculateRko($warehouse, $projectionDays) : [];
        }

        // Apply same filtering as Livewire
        $data = collect($suggestions)->filter(function($item) use ($search, $ven, $abc) {
            $matchesSearch = empty($search) || stripos($item['item_name'], $search) !== false || stripos($item['code'], $search) !== false;
            $matchesVen = empty($ven) || $item['ven'] === $ven;
            $matchesAbc = empty($abc) || $item['abc'] === $abc;
            return $matchesSearch && $matchesVen && $matchesAbc;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.procurement.rko-pdf', [
            'data' => $data,
            'warehouse' => ($warehouseId === 'all') ? 'Global' : \App\Models\Warehouse::find($warehouseId)?->name,
            'days' => $projectionDays,
            'date' => now()->format('d F Y')
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('RKO-' . now()->format('Ymd') . '.pdf');
    }
}
