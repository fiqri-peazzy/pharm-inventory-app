<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposals', function (Blueprint $table) {
            $table->id();
            $table->string('disposal_number')->unique();
            $table->foreignId('warehouse_id')->constrained();
            $table->enum('type', ['disposal', 'return_to_supplier'])->default('disposal');
            $table->date('disposal_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('disposal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained();
            $table->foreignId('item_batch_id')->constrained();
            $table->decimal('qty', 15, 2);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposal_details');
        Schema::dropIfExists('disposals');
    }
};
