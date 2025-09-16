<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\RealTimeBalanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestBalanceCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:balance-calculation {--employee=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the balance calculation for employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $employeeId = $this->option('employee');
        
        if ($employeeId) {
            $this->testSingleEmployee($employeeId);
        } else {
            $this->testApril2024Joiners();
        }
    }

    private function testSingleEmployee($employeeId)
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            $this->error("Employee with ID {$employeeId} not found");
            return;
        }

        $this->displayEmployeeBalance($employee);
    }

    private function testApril2024Joiners()
    {
        $this->info("Testing balance calculation for April 2024 joiners...");
        
        $employees = Employee::whereMonth('company_doj', 4)
            ->whereYear('company_doj', 2024)
            ->take(5)
            ->get();

        if ($employees->isEmpty()) {
            $this->info("No employees found who joined in April 2024");
            return;
        }

        foreach ($employees as $employee) {
            $this->displayEmployeeBalance($employee);
            $this->line('');
        }
    }

    private function displayEmployeeBalance($employee)
    {
        $this->info("Employee: {$employee->name} (ID: {$employee->id})");
        $this->info("Company DOJ: {$employee->company_doj}");
        $this->info("Adjusted DOJ: {$employee->adjusted_doj->format('Y-m-d')}");
        $this->info("Accrual Start Month: {$employee->accrual_start_month->format('Y-m')}");

        // Get balance breakdown
        $breakdown = $employee->getBalanceBreakdown();
        
        $this->info("Total Accrued: {$breakdown['total_accrued']} days");
        $this->info("Total Taken: {$breakdown['total_taken']} days");
        $this->info("Pending Applications: {$breakdown['pending_applications']} days");
        $this->info("Current Balance: {$breakdown['current_balance']} days");
        $this->info("Available Balance: {$breakdown['available_balance']} days");

        // Compare with old balance
        $oldBalance = $employee->getOriginal('paid_leave_balance') ?? 0;
        $this->info("Old Stored Balance: {$oldBalance} days");
        
        if (abs($breakdown['current_balance'] - $oldBalance) > 0.01) {
            $this->warn("⚠️  Balance discrepancy detected!");
        } else {
            $this->info("✅ Balance is consistent");
        }
    }
}
