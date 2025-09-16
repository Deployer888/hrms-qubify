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
        Schema::create('leave_accrual_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('year_month', 7); // Format: YYYY-MM
            $table->decimal('amount', 6, 2);
            $table->enum('source', ['cron', 'backfill', 'manual']);
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate cron accruals
            $table->unique(['employee_id', 'year_month', 'source'], 'unique_cron_accrual');
            
            // Indexes for performance
            $table->index(['employee_id', 'year_month']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_accrual_ledger');
    }
};
