<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\BackfillService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillBalancesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:backfill-balances 
                            {--from= : Start month (YYYY-MM format)}
                            {--to= : End month (YYYY-MM format)}
                            {--employee= : Specific employee ID to process}
                            {--batch-size=200 : Number of employees to process per batch}
                            {--dry-run : Run without making actual changes}
                            {--report : Generate balance analysis report only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill and correct historical leave balances for employees';

    protected BackfillService $backfillService;

    public function __construct(BackfillService $backfillService)
    {
        parent::__construct();
        $this->backfillService = $backfillService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        
        // Parse options
        $fromMonth = $this->option('from') 
            ? Carbon::createFromFormat('Y-m', $this->option('from'), 'Asia/Kolkata')
            : Carbon::parse('2024-04-01', 'Asia/Kolkata');
            
        $toMonth = $this->option('to')
            ? Carbon::createFromFormat('Y-m', $this->option('to'), 'Asia/Kolkata')
            : Carbon::now('Asia/Kolkata');
            
        $employeeId = $this->option('employee');
        $batchSize = (int) $this->option('batch-size');
        $isDryRun = $this->option('dry-run');
        $isReportOnly = $this->option('report');

        $this->info("Starting backfill process from {$fromMonth->format('Y-m')} to {$toMonth->format('Y-m')}");
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }
        
        if ($isReportOnly) {
            $this->info('REPORT MODE - Generating balance analysis report only');
        }

        try {
            if ($isReportOnly) {
                $this->generateReport($toMonth);
            } elseif ($employeeId) {
                $this->processSingleEmployee($employeeId, $fromMonth, $toMonth, $isDryRun);
            } else {
                $this->processBatchEmployees($batchSize, $fromMonth, $toMonth, $isDryRun);
            }
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->info("Processing completed in {$duration} seconds");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error during processing: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Process a single employee.
     */
    private function processSingleEmployee(int $employeeId, Carbon $fromMonth, Carbon $toMonth, bool $isDryRun): void
    {
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            $this->error("Employee with ID {$employeeId} not found");
            return;
        }

        $this->info("Processing employee: {$employee->name} (ID: {$employee->id})");
        
        if ($isDryRun) {
            $analysis = $this->backfillService->getEmployeeBalanceAnalysis($employee, $toMonth);
            $this->displayEmployeeAnalysis($analysis);
        } else {
            $result = $this->backfillService->backfillEmployee($employee, $fromMonth, $toMonth);
            $this->displayEmployeeResult($result);
        }
    }

    /**
     * Process employees in batches.
     */
    private function processBatchEmployees(int $batchSize, Carbon $fromMonth, Carbon $toMonth, bool $isDryRun): void
    {
        $this->info("Processing all active employees in batches of {$batchSize}");
        
        if ($isDryRun) {
            $this->simulateBatchProcessing($batchSize, $toMonth);
        } else {
            $results = $this->backfillService->batchBackfillEmployees($batchSize, $fromMonth, $toMonth);
            $this->displayBatchResults($results);
        }
    }

    /**
     * Generate balance analysis report.
     */
    private function generateReport(Carbon $asOfMonth): void
    {
        $this->info("Generating balance analysis report as of {$asOfMonth->format('Y-m')}");
        
        $report = $this->backfillService->generateBackfillReport($asOfMonth);
        
        $this->info("\n" . str_repeat('=', 80));
        $this->info("BALANCE ANALYSIS REPORT - AS OF {$report['as_of_month']}");
        $this->info(str_repeat('=', 80));
        
        $this->info("Total employees: {$report['total_employees']}");
        $this->info("Employees needing correction: {$report['employees_needing_correction']}");
        $this->info("Total discrepancy amount: {$report['total_discrepancy']} days");
        
        // Show employees needing correction
        $needingCorrection = collect($report['employees'])->filter(fn($emp) => $emp['needs_correction']);
        
        if ($needingCorrection->isNotEmpty()) {
            $this->info("\nEmployees needing correction:");
            $this->table(
                ['ID', 'Name', 'Expected', 'Actual', 'Discrepancy', 'Eligible Months'],
                $needingCorrection->map(function ($emp) {
                    return [
                        $emp['employee_id'],
                        $emp['employee_name'],
                        $emp['expected_total_balance'],
                        $emp['actual_balance'],
                        $emp['discrepancy'],
                        $emp['eligible_months_count']
                    ];
                })->toArray()
            );
        } else {
            $this->info("\nAll employee balances are correct!");
        }
    }

    /**
     * Simulate batch processing for dry run.
     */
    private function simulateBatchProcessing(int $batchSize, Carbon $asOfMonth): void
    {
        $this->info('Simulating batch processing...');
        
        $totalEmployees = Employee::where('is_active', 1)->count();
        $needingCorrection = 0;
        $totalDiscrepancy = 0.0;
        
        $progressBar = $this->output->createProgressBar($totalEmployees);
        $progressBar->start();
        
        Employee::where('is_active', 1)->chunk($batchSize, function ($employees) use (&$needingCorrection, &$totalDiscrepancy, $asOfMonth, $progressBar) {
            foreach ($employees as $employee) {
                $analysis = $this->backfillService->getEmployeeBalanceAnalysis($employee, $asOfMonth);
                
                if ($analysis['needs_correction']) {
                    $needingCorrection++;
                    $totalDiscrepancy += $analysis['discrepancy'];
                }
                
                $progressBar->advance();
            }
        });
        
        $progressBar->finish();
        
        $this->info("\n\nDry run results:");
        $this->info("Total employees: {$totalEmployees}");
        $this->info("Would correct: {$needingCorrection} employees");
        $this->info("Total correction amount: {$totalDiscrepancy} days");
    }

    /**
     * Display results for a single employee.
     */
    private function displayEmployeeResult(array $result): void
    {
        $this->info("\nResult for {$result['employee_name']} (ID: {$result['employee_id']}):");
        
        if ($result['correction_applied']) {
            $this->info("✓ Correction applied: {$result['discrepancy']} days");
            $this->info("New balance: {$result['new_balance']} days");
        } else {
            $this->info("✓ {$result['reason']}");
        }
    }

    /**
     * Display analysis for a single employee.
     */
    private function displayEmployeeAnalysis(array $analysis): void
    {
        $this->info("\nAnalysis for {$analysis['employee_name']} (ID: {$analysis['employee_id']}):");
        $this->info("Adjusted DOJ: {$analysis['adjusted_doj']}");
        $this->info("Accrual start month: {$analysis['accrual_start_month']}");
        $this->info("Eligible months: {$analysis['eligible_months_count']}");
        $this->info("Expected balance: {$analysis['expected_total_balance']} days");
        $this->info("Actual balance: {$analysis['actual_balance']} days");
        $this->info("Discrepancy: {$analysis['discrepancy']} days");
        
        if ($analysis['needs_correction']) {
            $this->warn("⚠ This employee needs correction");
        } else {
            $this->info("✓ Balance is correct");
        }
    }

    /**
     * Display batch processing results.
     */
    private function displayBatchResults(array $results): void
    {
        $this->info("\n" . str_repeat('=', 60));
        $this->info("BATCH BACKFILL RESULTS");
        $this->info(str_repeat('=', 60));
        
        $this->info("Total processed: {$results['total_processed']} employees");
        $this->info("Corrections applied: {$results['corrections_applied']} employees");
        $this->info("Total correction amount: {$results['total_correction_amount']} days");
        
        if ($results['errors'] > 0) {
            $this->error("Errors: {$results['errors']} employees");
        }
        
        // Show summary of corrections if any
        $corrected = collect($results['employees'])->filter(fn($emp) => $emp['correction_applied'] ?? false);
        
        if ($corrected->isNotEmpty() && $this->option('verbose')) {
            $this->info("\nCorrected employees:");
            $this->table(
                ['ID', 'Name', 'Correction Amount', 'New Balance'],
                $corrected->map(function ($emp) {
                    return [
                        $emp['employee_id'],
                        $emp['employee_name'],
                        $emp['discrepancy'] ?? 'N/A',
                        $emp['new_balance'] ?? 'N/A'
                    ];
                })->toArray()
            );
        }
    }
}
