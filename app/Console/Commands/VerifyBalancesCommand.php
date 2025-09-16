<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\BalanceVerificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyBalancesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'leaves:verify-balances 
                            {--employee= : Specific employee ID to verify}
                            {--fix : Automatically fix identified discrepancies}
                            {--report : Generate detailed report}
                            {--dry-run : Preview changes without applying them}';

    /**
     * The console command description.
     */
    protected $description = 'Verify and optionally fix paid leave balance discrepancies';

    protected BalanceVerificationService $verificationService;

    public function __construct(BalanceVerificationService $verificationService)
    {
        parent::__construct();
        $this->verificationService = $verificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting leave balance verification...');
        
        $employeeId = $this->option('employee');
        $shouldFix = $this->option('fix');
        $generateReport = $this->option('report');
        $isDryRun = $this->option('dry-run') || !$shouldFix;

        if ($employeeId) {
            return $this->verifySpecificEmployee($employeeId, $shouldFix, $isDryRun);
        } else {
            return $this->verifyAllEmployees($shouldFix, $generateReport, $isDryRun);
        }
    }

    /**
     * Verify specific employee balance.
     */
    private function verifySpecificEmployee(int $employeeId, bool $shouldFix, bool $isDryRun): int
    {
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            $this->error("Employee with ID {$employeeId} not found.");
            return 1;
        }

        $this->info("Verifying balance for employee: {$employee->name} (ID: {$employeeId})");
        
        $verification = $this->verificationService->verifyEmployeeBalance($employee);
        
        $this->displayEmployeeVerification($verification);
        
        if (!$verification['is_correct'] && $shouldFix) {
            $this->info("\nApplying fixes...");
            $fixes = $this->verificationService->fixEmployeeDiscrepancies($employee, $isDryRun);
            $this->displayFixResults($fixes);
        }

        return 0;
    }

    /**
     * Verify all employees' balances.
     */
    private function verifyAllEmployees(bool $shouldFix, bool $generateReport, bool $isDryRun): int
    {
        $this->info('Verifying balances for all active employees...');
        
        $verification = $this->verificationService->verifyAllEmployeeBalances();
        
        $this->displaySummary($verification['summary']);
        
        if ($generateReport) {
            $this->generateDetailedReport($verification);
        }

        $incorrectBalances = collect($verification['results'])
            ->filter(fn($result) => !$result['is_correct']);

        if ($incorrectBalances->count() > 0) {
            $this->warn("\nFound {$incorrectBalances->count()} employees with incorrect balances:");
            
            $this->table(
                ['Employee ID', 'Name', 'Expected', 'Actual', 'Discrepancy', 'Missing Months'],
                $incorrectBalances->map(function ($result) {
                    return [
                        $result['employee_id'],
                        $result['employee_name'],
                        $result['expected_balance'],
                        $result['actual_accrued'],
                        $result['balance_discrepancy'],
                        count($result['missing_months']),
                    ];
                })->toArray()
            );

            if ($shouldFix) {
                return $this->fixAllDiscrepancies($incorrectBalances, $isDryRun);
            } else {
                $this->info("\nTo fix these discrepancies, run: php artisan leaves:verify-balances --fix");
            }
        } else {
            $this->info("\n✅ All employee balances are correct!");
        }

        return 0;
    }

    /**
     * Fix discrepancies for all employees.
     */
    private function fixAllDiscrepancies($incorrectBalances, bool $isDryRun): int
    {
        $this->info($isDryRun ? "\nDRY RUN - Previewing fixes:" : "\nApplying fixes:");
        
        $totalFixed = 0;
        $totalActions = 0;

        DB::beginTransaction();
        
        try {
            foreach ($incorrectBalances as $result) {
                $employee = Employee::find($result['employee_id']);
                if (!$employee) continue;

                $fixes = $this->verificationService->fixEmployeeDiscrepancies($employee, $isDryRun);
                
                if (!empty($fixes['actions_taken'])) {
                    $totalFixed++;
                    $totalActions += count($fixes['actions_taken']);
                    
                    $this->info("Fixed {$employee->name} (ID: {$employee->id}) - " . count($fixes['actions_taken']) . " actions");
                }
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info("\n✅ Successfully fixed {$totalFixed} employees with {$totalActions} total actions.");
            } else {
                DB::rollBack();
                $this->info("\n📋 Would fix {$totalFixed} employees with {$totalActions} total actions.");
                $this->info("Run without --dry-run to apply these changes.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error during fix process: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Display employee verification results.
     */
    private function displayEmployeeVerification(array $verification): void
    {
        $this->info("\n" . str_repeat('=', 60));
        $this->info("EMPLOYEE BALANCE VERIFICATION");
        $this->info(str_repeat('=', 60));
        
        $this->info("Employee: {$verification['employee_name']} (ID: {$verification['employee_id']})");
        $this->info("Date of Joining: {$verification['doj']}");
        $this->info("Adjusted DOJ: {$verification['adjusted_doj']}");
        $this->info("Accrual Start: {$verification['accrual_start_month']}");
        $this->info("Expected Eligible Months: {$verification['expected_eligible_months']}");
        
        $this->info("\nBalance Details:");
        $this->info("Expected Balance: {$verification['expected_balance']}");
        $this->info("Actual Accrued: {$verification['actual_accrued']}");
        $this->info("Total Taken: {$verification['actual_taken']}");
        $this->info("Current Balance: {$verification['current_balance']}");
        $this->info("Available Balance: {$verification['available_balance']}");
        
        if ($verification['is_correct']) {
            $this->info("\n✅ Balance is CORRECT");
        } else {
            $this->error("\n❌ Balance is INCORRECT");
            $this->error("Discrepancy: {$verification['balance_discrepancy']}");
            
            if (!empty($verification['missing_months'])) {
                $this->error("Missing Months: " . implode(', ', $verification['missing_months']));
            }
        }
    }

    /**
     * Display summary of verification results.
     */
    private function displaySummary(array $summary): void
    {
        $this->info("\n" . str_repeat('=', 60));
        $this->info("BALANCE VERIFICATION SUMMARY");
        $this->info(str_repeat('=', 60));
        
        $this->info("Total Employees: {$summary['total_employees']}");
        $this->info("Correct Balances: {$summary['correct_balances']}");
        $this->error("Incorrect Balances: {$summary['incorrect_balances']}");
        $this->info("Total Discrepancy: {$summary['total_discrepancy']}");
        $this->info("Employees with Missing Months: {$summary['employees_with_missing_months']}");
        
        $accuracy = $summary['total_employees'] > 0 
            ? round(($summary['correct_balances'] / $summary['total_employees']) * 100, 2)
            : 0;
        
        $this->info("Accuracy: {$accuracy}%");
    }

    /**
     * Generate detailed report.
     */
    private function generateDetailedReport(array $verification): void
    {
        $report = $this->verificationService->generateBalanceReport();
        
        $this->info("\n" . str_repeat('=', 60));
        $this->info("DETAILED BALANCE REPORT");
        $this->info(str_repeat('=', 60));
        
        if (!empty($report['recommendations'])) {
            $this->info("\nRecommendations:");
            foreach ($report['recommendations'] as $recommendation) {
                $this->info("• {$recommendation['action']} ({$recommendation['count']} employees)");
                $this->info("  Command: {$recommendation['command']}");
            }
        }

        if (!empty($report['incorrect_balances'])) {
            $this->info("\nDetailed Issues:");
            foreach ($report['incorrect_balances'] as $result) {
                $this->info("\n{$result['employee_name']} (ID: {$result['employee_id']}):");
                $this->info("  Expected: {$result['expected_balance']}, Actual: {$result['actual_accrued']}");
                
                if (!empty($result['missing_months'])) {
                    $this->info("  Missing: " . implode(', ', $result['missing_months']));
                }
            }
        }
    }

    /**
     * Display fix results.
     */
    private function displayFixResults(array $fixes): void
    {
        if (empty($fixes['actions_taken'])) {
            $this->info("No fixes needed for this employee.");
            return;
        }

        $this->info("\nActions taken:");
        foreach ($fixes['actions_taken'] as $action) {
            $status = $action['status'] === 'completed' ? '✅' : '📋';
            $this->info("{$status} {$action['type']}: {$action['year_month']} (+{$action['amount']})");
        }
    }
}