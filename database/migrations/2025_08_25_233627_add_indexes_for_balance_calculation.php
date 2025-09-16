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
        Schema::table('leaves', function (Blueprint $table) {
            // Optimize leave queries for balance calculation
            $table->index(['employee_id', 'leave_type_id', 'status'], 'idx_leaves_employee_type_status');
        });

        Schema::table('leave_accrual_ledger', function (Blueprint $table) {
            // Optimize accrual ledger queries
            $table->index(['employee_id'], 'idx_accrual_ledger_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropIndex('idx_leaves_employee_type_status');
        });

        Schema::table('leave_accrual_ledger', function (Blueprint $table) {
            $table->dropIndex('idx_accrual_ledger_employee');
        });
    }
};
