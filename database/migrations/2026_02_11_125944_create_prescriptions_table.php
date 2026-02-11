<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_number')->unique();
            $table->string('patient_id')->nullable();
            $table->string('patient_name');
            $table->string('medical_record_number')->nullable();
            $table->foreignId('doctor_id')->constrained('users');
            $table->string('doctor_name')->nullable();
            $table->foreignId('service_unit_id')->nullable()->constrained();
            $table->foreignId('warehouse_id')->nullable()->constrained(); // Pharmacy/Depo to dispense from
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('prescription_date');
            $table->enum('status', ['draft', 'submitted', 'processing', 'completed', 'cancelled'])->default('submitted');
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prescription_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained();
            $table->foreignId('item_batch_id')->nullable()->constrained(); // Filled when dispensed
            $table->integer('qty');
            $table->decimal('price_per_unit', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('instruction')->nullable(); // Aturan pakai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_details');
        Schema::dropIfExists('prescriptions');
    }
};
