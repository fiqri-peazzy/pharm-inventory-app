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
        Schema::create('disposal_witnesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('role'); // e.g., Petugas Gudang, Keuangan, Saksi Luar
            $table->string('signature_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposal_witnesses');
    }
};
