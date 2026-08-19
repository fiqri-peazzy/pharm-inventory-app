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
        Schema::create('ai_daily_briefings', function (Blueprint $table) {
            $table->id();
            $table->date('briefing_date')->unique();
            $table->text('content');
            $table->unsignedInteger('near_expired_count')->default(0);
            $table->unsignedInteger('critical_stock_count')->default(0);
            $table->boolean('generated_by_ai')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_daily_briefings');
    }
};
