<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ASSETS (1xxxx)
        $assets = Account::create([
            'code' => '10000',
            'name' => 'ASSETS',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'description' => 'Aset'
        ]);

        $currentAssets = Account::create([
            'code' => '11000',
            'name' => 'Current Assets',
            'type' => 'asset',
            'parent_id' => $assets->id,
            'normal_balance' => 'debit',
            'description' => 'Aset Lancar'
        ]);

        $inventoryAccounts = [
            ['code' => '11100', 'name' => 'Inventory - Medicines', 'desc' => 'Persediaan Obat'],
            ['code' => '11200', 'name' => 'Inventory - Medical Supplies', 'desc' => 'Persediaan Alkes'],
            ['code' => '11300', 'name' => 'Inventory - Reagents', 'desc' => 'Persediaan Reagensia'],
            ['code' => '11400', 'name' => 'Inventory - Medical Equipment', 'desc' => 'Persediaan Alat Medis'],
            ['code' => '11900', 'name' => 'Inventory - Others', 'desc' => 'Persediaan Lainnya'],
        ];

        foreach ($inventoryAccounts as $acc) {
            Account::create([
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => 'asset',
                'parent_id' => $currentAssets->id,
                'normal_balance' => 'debit',
                'description' => $acc['desc']
            ]);
        }

        // LIABILITIES (2xxxx)
        $liabilities = Account::create([
            'code' => '20000',
            'name' => 'LIABILITIES',
            'type' => 'liability',
            'normal_balance' => 'credit',
            'description' => 'Kewajiban'
        ]);

        $currentLiabilities = Account::create([
            'code' => '21000',
            'name' => 'Current Liabilities',
            'type' => 'liability',
            'parent_id' => $liabilities->id,
            'normal_balance' => 'credit',
            'description' => 'Kewajiban Jangka Pendek'
        ]);

        $apAccounts = [
            ['code' => '21100', 'name' => 'Accounts Payable - Suppliers', 'desc' => 'Hutang Dagang - Supplier'],
            ['code' => '21900', 'name' => 'Accounts Payable - Others', 'desc' => 'Hutang Lainnya'],
        ];

        foreach ($apAccounts as $acc) {
            Account::create([
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => 'liability',
                'parent_id' => $currentLiabilities->id,
                'normal_balance' => 'credit',
                'description' => $acc['desc']
            ]);
        }

        // EQUITY (3xxxx)
        Account::create([
            'code' => '30000',
            'name' => 'EQUITY',
            'type' => 'equity',
            'normal_balance' => 'credit',
            'description' => 'Ekuitas'
        ]);

        // REVENUE (4xxxx)
        $revenue = Account::create([
            'code' => '40000',
            'name' => 'REVENUE',
            'type' => 'revenue',
            'normal_balance' => 'credit',
            'description' => 'Pendapatan'
        ]);

        $revenueAccounts = [
            ['code' => '41000', 'name' => 'Pharmacy Revenue', 'desc' => 'Pendapatan Farmasi'],
            ['code' => '49000', 'name' => 'Other Revenue', 'desc' => 'Pendapatan Lainnya'],
            ['code' => '49100', 'name' => 'Adjustment Income', 'desc' => 'Pendapatan Penyesuaian'],
        ];

        foreach ($revenueAccounts as $acc) {
            Account::create([
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => 'revenue',
                'parent_id' => $revenue->id,
                'normal_balance' => 'credit',
                'description' => $acc['desc']
            ]);
        }

        // EXPENSES (5xxxx)
        $expenses = Account::create([
            'code' => '50000',
            'name' => 'EXPENSES',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'description' => 'Beban'
        ]);

        $cogsParent = Account::create([
            'code' => '51000',
            'name' => 'Cost of Goods Sold',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'normal_balance' => 'debit',
            'description' => 'Harga Pokok Penjualan'
        ]);

        $cogsAccounts = [
            ['code' => '51100', 'name' => 'COGS - Medicines', 'desc' => 'HPP Obat'],
            ['code' => '51200', 'name' => 'COGS - Medical Supplies', 'desc' => 'HPP Alkes'],
            ['code' => '51900', 'name' => 'COGS - Others', 'desc' => 'HPP Lainnya'],
        ];

        foreach ($cogsAccounts as $acc) {
            Account::create([
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => 'expense',
                'parent_id' => $cogsParent->id,
                'normal_balance' => 'debit',
                'description' => $acc['desc']
            ]);
        }

        $opexParent = Account::create([
            'code' => '52000',
            'name' => 'Operating Expenses',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'normal_balance' => 'debit',
            'description' => 'Beban Operasional'
        ]);

        $opexAccounts = [
            ['code' => '52100', 'name' => 'Stock Loss', 'desc' => 'Kerugian Stok'],
            ['code' => '52200', 'name' => 'Disposal Loss', 'desc' => 'Rugi Pemusnahan'],
            ['code' => '52300', 'name' => 'Shrinkage', 'desc' => 'Penyusutan'],
            ['code' => '52900', 'name' => 'Adjustment Expense', 'desc' => 'Beban Penyesuaian'],
        ];

        foreach ($opexAccounts as $acc) {
            Account::create([
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => 'expense',
                'parent_id' => $opexParent->id,
                'normal_balance' => 'debit',
                'description' => $acc['desc']
            ]);
        }
    }
}
