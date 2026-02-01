<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('employee_id')->nullable()->after('username');
            $table->string('phone')->nullable()->after('email');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->after('phone');
            $table->boolean('is_active')->default(true)->after('warehouse_id');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->unsignedBigInteger('created_by')->nullable()->after('last_login_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'username',
                'employee_id',
                'phone',
                'warehouse_id',
                'is_active',
                'last_login_at',
                'created_by',
                'updated_by'
            ]);
        });
    }
};