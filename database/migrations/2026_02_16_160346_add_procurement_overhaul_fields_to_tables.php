<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('sp_type', ['reguler', 'narkotika', 'psikotropika'])->default('reguler')->after('po_number');
        });

        Schema::table('item_batches', function (Blueprint $table) {
            $table->enum('status', ['quarantine', 'available', 'reserved', 'expired'])->default('available')->after('is_active');
        });

        Schema::table('receiving_details', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('purchase_price');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('sp_type');
        });

        Schema::table('item_batches', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('receiving_details', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'discount_amount']);
        });
    }
};
