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
        Schema::create('disposal_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->enum('type', ['before', 'process', 'after'])->default('process');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposal_evidences');
    }
};
