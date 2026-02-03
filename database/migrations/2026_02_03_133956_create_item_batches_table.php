<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->string('batch_number');
            $table->date('expired_date');
            $table->integer('initial_qty');
            $table->integer('current_qty');
            $table->decimal('purchase_price', 15, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('item_batch_id')->nullable()->constrained();
            $table->date('transaction_date');
            $table->string('reference_type'); // receiving, distribution, prescription, etc.
            $table->unsignedBigInteger('reference_id');
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->integer('last_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_cards');
        Schema::dropIfExists('item_batches');
    }
};
