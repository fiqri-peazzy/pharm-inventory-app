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

    public function quarantine()
    {
        return view('pages.inventory.quarantine');
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

    public function adjustments()
    {
        return view('pages.inventory.adjustments.index');
    }

    public function createAdjustment()
    {
        return view('pages.inventory.adjustments.form');
    }

    public function editAdjustment($id)
    {
        return view('pages.inventory.adjustments.form', [
            'adjustmentId' => $id
        ]);
    }

    public function returns()
    {
        return view('pages.inventory.returns.index');
    }

    public function createReturn()
    {
        return view('pages.inventory.returns.form');
    }

    public function editReturn($id)
    {
        return view('pages.inventory.returns.form', [
            'returnId' => $id
        ]);
    }

    public function initialImport()
    {
        return view('pages.inventory.initial-import');
    }
}
