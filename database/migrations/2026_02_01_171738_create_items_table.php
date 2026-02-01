<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nie_number')->nullable();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->foreignId('item_category_id')->constrained('item_categories')->onDelete('restrict');
            $table->string('manufacturer')->nullable();
            $table->foreignId('item_unit_id')->constrained('item_units')->onDelete('restrict');
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->boolean('is_prescription')->default(false);
            $table->boolean('is_consignment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('storage_condition', ['suhu_ruang', 'kulkas', 'freezer'])->default('suhu_ruang');
            $table->string('fornas_status')->nullable();
            $table->string('fornas_code')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('generic_name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};