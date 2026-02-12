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
        Schema::table('disposals', function (Blueprint $table) {
            $table->string('disposal_type')->after('warehouse_id')->nullable(); // Expired, Damaged, Lost, etc.
            $table->string('method')->after('disposal_type')->nullable(); // Incineration, etc.
            $table->string('ba_number')->after('method')->nullable();
            $table->string('location')->after('ba_number')->nullable();
            $table->date('execution_date')->after('disposal_date')->nullable();
            $table->string('witness_1')->after('execution_date')->nullable();
            $table->string('witness_2')->after('witness_1')->nullable();
            $table->string('witness_3')->after('witness_2')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('disposals', function (Blueprint $table) {
            $table->dropColumn([
                'disposal_type', 'method', 'ba_number', 'location', 
                'execution_date', 'witness_1', 'witness_2', 'witness_3'
            ]);
        });
    }
};
