<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function dashboard($warehouse = null)
    {
        return view('pages.inventory.dashboard', [
            'warehouseId' => $warehouse
        ]);
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

    public function thresholds()
    {
        return view('pages.inventory.thresholds');
    }

    public function editDisposal($id)
    {
        return view('pages.inventory.disposals.form', [
            'disposalId' => $id
        ]);
    }

    public function distributions()
    {
        return view('pages.inventory.distributions.index');
    }

    public function createDistributionRequest()
    {
        return view('pages.inventory.distributions.request');
    }

    public function processDistribution($id)
    {
        return view('pages.inventory.distributions.process', [
            'distributionId' => $id
        ]);
    }

    public function receiveDistribution($id)
    {
        return view('pages.inventory.distributions.receive', [
            'distributionId' => $id
        ]);
    }

    public function stockCards()
    {
        return view('pages.inventory.stocks.cards');
    }

    public function batches()
    {
        return view('pages.inventory.stocks.batches');
    }

    public function stockOpnames()
    {
        return view('pages.inventory.stock-opnames.index');
    }

    public function createStockOpname()
    {
        return view('pages.inventory.stock-opnames.form');
    }

    public function editStockOpname($id)
    {
        return view('pages.inventory.stock-opnames.form', [
            'opnameId' => $id
        ]);
    }
}
