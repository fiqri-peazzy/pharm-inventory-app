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
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->decimal('total_value', 15, 2)->default(0)->after('notes');
            $table->string('evidence_file')->nullable()->after('total_value');
            $table->text('investigation_report')->nullable()->after('evidence_file');
            $table->text('corrective_action')->nullable()->after('investigation_report');
            // Change type to plus/minus
            $table->string('type')->default('plus')->change();
        });

        Schema::table('stock_adjustment_details', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->default(0)->after('difference');
            $table->decimal('total_value', 15, 2)->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn(['total_value', 'evidence_file', 'investigation_report', 'corrective_action']);
        });

        Schema::table('stock_adjustment_details', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_value']);
        });
    }
};
