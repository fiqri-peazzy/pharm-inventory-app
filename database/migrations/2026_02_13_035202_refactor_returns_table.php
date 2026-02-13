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
        Schema::table('returns', function (Blueprint $table) {
            $table->string('reason_category')->nullable()->after('return_date');
            $table->string('receiving_number')->nullable()->after('reason_category');
            $table->string('po_number')->nullable()->after('receiving_number');
            $table->string('invoice_number')->nullable()->after('po_number');
            $table->string('supplier_do_number')->nullable()->after('invoice_number');
            $table->string('evidence_file')->nullable()->after('supplier_do_number');
            $table->decimal('total_value', 15, 2)->default(0)->after('evidence_file');
            
            // Re-define status enum to match the new multi-stage workflow
            $table->string('status')->default('draft')->change();
            // In a real scenario, we might want to cast this or use a more flexible approach,
            // but for now, changing it to string to allow easier status additions.
        });

        Schema::table('return_details', function (Blueprint $table) {
            $table->decimal('total_value', 15, 2)->default(0)->after('price');
        });

        Schema::table('return_credit_notes', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('note_date'); // pending, received, applied
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn([
                'reason_category', 'receiving_number', 'po_number', 
                'invoice_number', 'supplier_do_number', 'evidence_file', 'total_value'
            ]);
        });

        Schema::table('return_details', function (Blueprint $table) {
            $table->dropColumn('total_value');
        });

        Schema::table('return_credit_notes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
