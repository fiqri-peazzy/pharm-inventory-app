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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('purchase_request_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('po_date');
            $table->date('expected_delivery_date')->nullable();
            $table->integer('payment_term')->default(30);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('ppn_amount', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->enum('status', ['draft', 'sent', 'partial_received', 'completed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
