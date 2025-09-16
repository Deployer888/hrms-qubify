<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayrollFieldsToPaySlipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_slips', function (Blueprint $table) {
            $table->integer('actual_payable_days')->nullable()->comment('Number of days an employee is paid for');
            $table->integer('total_working_days')->nullable()->comment('Total working days in the period');
            $table->integer('loss_of_pay_days')->nullable()->comment('Days deducted for loss of pay');
            $table->integer('days_payable')->nullable()->comment('Final days payable after deductions');
            $table->decimal('hra', 10, 2)->nullable()->comment('House Rent Allowance - 40% of salary');
            $table->decimal('tds', 10, 2)->nullable()->comment('Tax Deducted at Source');
            $table->decimal('special_allowance', 10, 2)->nullable()->comment('Special Allowance - 20% of salary');
            $table->decimal('total_earnings', 10, 2)->nullable()->comment('Total Earnings for the period');
            $table->decimal('total_deduction', 10, 2)->nullable()->comment('Total deductions for the period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_slips', function (Blueprint $table) {
            $table->dropColumn([
                'actual_payable_days',
                'total_working_days',
                'loss_of_pay_days',
                'days_payable',
                'basic',
                'hra',
                'tds',
                'special_allowance',
                'total_earnings',
                'total_deduction'
            ]);
        });
    }
}
