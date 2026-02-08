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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['gudang_utama', 'depo_farmasi', 'depo_ok', 'depo_igd', 'depo_ranap', 'depo_rajal', 'depo_bmhp']);
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->text('address')->nullable();
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
        Schema::dropIfExists('warehouses');
    }
};
