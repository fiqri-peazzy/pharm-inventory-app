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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('request_date');
            $table->integer('period_month');
            $table->integer('period_year');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'closed'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
