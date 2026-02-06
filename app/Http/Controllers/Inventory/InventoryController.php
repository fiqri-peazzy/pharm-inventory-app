<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function dashboard()
    {
        return view('pages.inventory.dashboard');
    }

    public function stockReport()
    {
        return view('pages.inventory.stock-report');
    }

    public function disposals()
    {
        return view('pages.inventory.disposals.index');
    }

    public function createDisposal()
    {
        return view('pages.inventory.disposals.form');
    }

    public function editDisposal($id)
    {
        return view('pages.inventory.disposals.form', [
            'disposalId' => $id
        ]);
    }
}
