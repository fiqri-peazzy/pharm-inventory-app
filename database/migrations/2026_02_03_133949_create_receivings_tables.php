<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->string('receiving_number')->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('receiving_date');
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('ppn_amount', 15, 2);
            $table->decimal('grand_total', 15, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'approved', 'posted'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('receiving_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained();
            $table->string('batch_number');
            $table->date('expired_date');
            $table->integer('qty_received');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('ppn_percentage', 5, 2)->default(11);
            $table->decimal('ppn_amount', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receiving_details');
        Schema::dropIfExists('receivings');
    }
};
