<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ward_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('service_unit_id')->constrained(); // Origin ward
            $table->foreignId('warehouse_id')->constrained(); // Source pharmacy/depo
            $table->date('request_date');
            $table->string('status')->default('requested'); // requested, approved, partially_fulfilled, fulfilled, rejected
            $table->text('notes')->nullable();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ward_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained();
            $table->integer('qty_requested');
            $table->integer('qty_fulfilled')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ward_request_details');
        Schema::dropIfExists('ward_requests');
    }
};
