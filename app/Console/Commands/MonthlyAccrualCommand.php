<?php

namespace App\Console\Commands;

use App\Services\MonthlyAccrualService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MonthlyAccrualCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:monthly-accrual 
                            {--month= : Specific month to process (YYYY-MM format)}
                            {--dry-run : Run without making actual changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly paid leave accruals for all eligible employees';

    protected MonthlyAccrualService $accrualService;

    public function __construct(MonthlyAccrualService $accrualService)
    {
        parent::__construct();
        $this->accrualService = $accrualService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lockKey = 'monthly_accrual_processing';
        
        // Prevent concurrent executions
        if (Cache::has($lockKey)) {
            $this->error('Monthly accrual processing is already running. Please wait for it to complete.');
            return 1;
        }

        // Set lock with 30-minute timeout
        Cache::put($lockKey, true, 1800);

        try {
            $startTime = microtime(true);
            
            // Determine which month to process
            $month = $this->option('month');
            $forMonth = $month 
                ? Carbon::createFromFormat('Y-m', $month, 'Asia/Kolkata')
                : Carbon::now('Asia/Kolkata')->subMonth(); // Previous month by default
                
            $isDryRun = $this->option('dry-run');
            
            $this->info("Starting monthly accrual processing for {$forMonth->format('Y-m')}");
            
            if ($isDryRun) {
                $this->warn('DRY RUN MODE - No actual changes will be made');
            }

            // Check if already processed
            if (!$isDryRun && $this->accrualService->isMonthProcessed($forMonth)) {
                $this->warn("Month {$forMonth->format('Y-m')} has already been processed.");
                
                if (!$this->confirm('Do you want to continue anyway?')) {
                    return 0;
                }
            }

            // Process accruals
            if ($isDryRun) {
                $results = $this->simulateProcessing($forMonth);
            } else {
                $results = $this->accrualService->processMonthlyAccruals($forMonth);
            }

            // Display results
            $this->displayResults($results, $forMonth, $isDryRun);
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->info("Processing completed in {$duration} seconds");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error during processing: " . $e->getMessage());
            return 1;
        } finally {
            Cache::forget($lockKey);
        }
    }

    /**
     * Simulate processing for dry run mode.
     */
    private function simulateProcessing(Carbon $forMonth): array
    {
        $this->info('Simulating accrual processing...');
        
        // This would normally call the service, but for dry run we just analyze
        $employees = \App\Models\Employee::where('is_active', 1)->get();
        $calculator = app(\App\Services\AccrualCalculator::class);
        
        $results = [
            'processed' => 0,
            'skipped' => 0,
            'errors' => 0,
            'total_amount' => 0.0,
            'employees' => []
        ];

        foreach ($employees as $employee) {
            if ($calculator->isEligibleForAccrual($employee, $forMonth)) {
                $results['processed']++;
                $results['total_amount'] += 1.5;
                $results['employees'][] = [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'amount' => 1.5,
                    'status' => 'would_process'
                ];
            } else {
                $results['skipped']++;
                $results['employees'][] = [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'reason' => 'Not eligible',
                    'status' => 'would_skip'
                ];
            }
        }

        return $results;
    }

    /**
     * Display processing results.
     */
    private function displayResults(array $results, Carbon $forMonth, bool $isDryRun): void
    {
        $this->info("\n" . str_repeat('=', 60));
        $this->info("MONTHLY ACCRUAL RESULTS FOR {$forMonth->format('Y-m')}");
        $this->info(str_repeat('=', 60));
        
        $verb = $isDryRun ? 'Would process' : 'Processed';
        $this->info("{$verb}: {$results['processed']} employees");
        $this->info("Skipped: {$results['skipped']} employees");
        
        if ($results['errors'] > 0) {
            $this->error("Errors: {$results['errors']} employees");
        }
        
        $this->info("Total amount: {$results['total_amount']} days");
        
        // Show detailed results if requested
        if ($this->option('verbose') || $results['errors'] > 0) {
            $this->info("\nDetailed Results:");
            $this->table(
                ['Employee ID', 'Name', 'Status', 'Amount/Reason'],
                collect($results['employees'])->map(function ($emp) {
                    return [
                        $emp['id'],
                        $emp['name'],
                        $emp['status'],
                        $emp['amount'] ?? $emp['reason'] ?? $emp['error'] ?? 'N/A'
                    ];
                })->toArray()
            );
        }
    }
}
