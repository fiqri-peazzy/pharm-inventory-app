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
        Schema::table('disposal_details', function (Blueprint $table) {
            if (!Schema::hasColumn('disposal_details', 'source_type')) {
                $table->string('source_type')->nullable()->after('reason'); // 'adjustment', 'opname'
            }
            if (!Schema::hasColumn('disposal_details', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }
        });

        Schema::table('return_details', function (Blueprint $table) {
            if (!Schema::hasColumn('return_details', 'source_type')) {
                $table->string('source_type')->nullable()->after('notes'); // 'adjustment', 'opname'
            }
            if (!Schema::hasColumn('return_details', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disposal_details', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'source_id']);
        });

        Schema::table('return_details', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'source_id']);
        });
    }
};
