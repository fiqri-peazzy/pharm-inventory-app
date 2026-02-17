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
        Schema::table('receivings', function (Blueprint $table) {
            if (!Schema::hasColumn('receivings', 'total_discount')) {
                $table->decimal('total_discount', 15, 2)->default(0)->after('ppn_amount');
            }
        });

        Schema::table('receiving_details', function (Blueprint $table) {
            if (!Schema::hasColumn('receiving_details', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('purchase_price');
            }
            if (!Schema::hasColumn('receiving_details', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receiving_details', function (Blueprint $table) {
            if (Schema::hasColumn('receiving_details', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('receiving_details', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
        });

        Schema::table('receivings', function (Blueprint $table) {
            if (Schema::hasColumn('receivings', 'total_discount')) {
                $table->dropColumn('total_discount');
            }
        });
    }
};
