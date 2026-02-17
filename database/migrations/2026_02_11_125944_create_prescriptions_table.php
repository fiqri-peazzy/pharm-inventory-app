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
            
            // Prescription Enhancement Fields
            $table->enum('payer_type', ['umum', 'bpjs', 'asuransi_lain'])->default('umum');
            $table->enum('patient_type', ['rj', 'ri'])->default('rj');
            $table->string('room_bed_number')->nullable();
            
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('prescription_date');
            $table->enum('status', ['draft', 'submitted', 'processing', 'completed', 'cancelled'])->default('submitted');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->boolean('is_returnable')->default(false);
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

        // Prescription Returns Tables
        Schema::create('prescription_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained();
            $table->enum('reason', ['pasien_pulang', 'pasien_meninggal', 'obat_berlebih', 'salah_dispensing', 'lainnya']);
            $table->text('notes')->nullable();
            $table->decimal('total_return_value', 15, 2)->default(0);
            $table->foreignId('returned_by')->constrained('users');
            $table->timestamp('returned_at');
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prescription_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_detail_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->foreignId('item_batch_id')->constrained();
            $table->integer('qty_returned');
            $table->decimal('price_per_unit', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->text('condition_notes')->nullable();
            $table->timestamps();
        });

        // Dosage Instructions Table
        Schema::create('dosage_instructions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('instruction');
            $table->string('frequency');
            $table->enum('timing', ['sebelum_makan', 'sesudah_makan', 'bersama_makan', 'bebas'])->nullable();
            $table->text('additional_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosage_instructions');
        Schema::dropIfExists('prescription_return_details');
        Schema::dropIfExists('prescription_returns');
        Schema::dropIfExists('prescription_details');
        Schema::dropIfExists('prescriptions');
    }
};
