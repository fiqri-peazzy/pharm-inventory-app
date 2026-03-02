<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseResetService
{
    /**
     * Tables to be truncated to clear all data while preserving users and auth structures.
     * Seeders are NOT touched, allowing the user to re-run them if needed.
     */
    protected $tablesToClear = [
        // Transactions
        'stock_cards',
        'item_batches',
        'receiving_details',
        'receivings',
        'purchase_order_details',
        'purchase_orders',
        'purchase_request_details',
        'purchase_requests',
        'distribution_details',
        'distributions',
        'prescription_details',
        'prescriptions',
        'prescription_return_details',
        'prescription_returns',
        'stock_opname_details',
        'stock_opnames',
        'stock_adjustment_details',
        'stock_adjustments',
        'disposal_details',
        'disposals',
        'disposal_witnesses',
        'disposal_evidences',
        'return_details',
        'returns',
        'journal_entry_details',
        'journal_entries',

        // Master Data
        'item_warehouse_settings',
        'item_prices',
        'item_conversions',
        'items',
        'item_categories',
        'item_units',
        'suppliers',
        'warehouses',
        'service_units',
        'accounts',
        'activity_logs',
    ];

    public function reset()
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tablesToClear as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

        return true;
    }
}
