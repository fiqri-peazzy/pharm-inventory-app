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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->string('distribution_number')->unique();
            
            $table->foreignId('origin_warehouse_id')->constrained('warehouses');
            $table->foreignId('destination_warehouse_id')->constrained('warehouses');
            
            $table->string('status')->default('draft'); // draft, requested, approved, sent, received, cancelled
            $table->string('type')->default('request'); // request, direct
            
            $table->text('notes')->nullable();
            
            $table->integer('total_items')->default(0);
            $table->decimal('total_qty', 15, 2)->default(0);
            
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('sent_by')->nullable()->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('distribution_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_id')->constrained('distributions')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('item_batch_id')->nullable()->constrained('item_batches');
            
            $table->decimal('qty_requested', 15, 2)->default(0);
            $table->decimal('qty_sent', 15, 2)->default(0);
            $table->decimal('qty_received', 15, 2)->default(0);
            
            $table->decimal('unit_price', 15, 2)->default(0); // Price at time of distribution
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_details');
        Schema::dropIfExists('distributions');
    }
};
