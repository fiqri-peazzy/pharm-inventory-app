<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL specific update to enum
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['draft', 'sent', 'partial_received', 'completed', 'cancelled'])->default('draft')->change();
        });
    }
};
