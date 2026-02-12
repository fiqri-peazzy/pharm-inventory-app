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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->enum('type', ['supplier', 'internal'])->default('supplier');
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->date('return_date');
            $table->string('reason');
            $table->enum('status', ['draft', 'submitted', 'approved', 'completed', 'rejected'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained();
            $table->foreignId('item_batch_id')->constrained();
            $table->decimal('qty', 15, 2);
            $table->decimal('price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('return_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained()->onDelete('cascade');
            $table->string('credit_note_number');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['refund', 'replacement', 'credit_memo'])->default('credit_memo');
            $table->date('note_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_credit_notes');
        Schema::dropIfExists('return_details');
        Schema::dropIfExists('returns');
    }
};
