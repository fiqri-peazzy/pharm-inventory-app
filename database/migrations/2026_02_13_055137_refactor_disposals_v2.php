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
        Schema::table('disposals', function (Blueprint $table) {
            // Refactor existing or add missing
            if (!Schema::hasColumn('disposals', 'disposal_method')) {
                $table->string('disposal_method')->after('method')->nullable();
            }
            if (!Schema::hasColumn('disposals', 'total_value')) {
                $table->decimal('total_value', 15, 2)->after('disposal_date')->default(0);
            }
            if (!Schema::hasColumn('disposals', 'approved_by')) {
                $table->foreignId('approved_by')->after('posted_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->after('approved_by')->nullable();
            }
            if (!Schema::hasColumn('disposals', 'executed_by')) {
                $table->foreignId('executed_by')->after('approved_at')->nullable()->constrained('users');
                $table->timestamp('executed_at')->after('executed_by')->nullable();
            }
            
            // Change status to string for more states
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('disposals', function (Blueprint $table) {
            // Rollback logic if needed, but usually we just keep it
        });
    }
};
