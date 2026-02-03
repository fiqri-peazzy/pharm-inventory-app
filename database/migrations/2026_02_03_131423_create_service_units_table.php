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
        Schema::create('service_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['poli', 'ruangan', 'instalasi']);
            $table->string('category')->nullable(); // poli_umum, poli_gigi, ruang_vip, ruang_kelas3, igd, ok, icu
            $table->foreignId('default_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_units');
    }
};
