<?php

namespace App\Console\Commands;

use App\Services\LeaveMonitoringService;
use Illuminate\Console\Command;

class MonitorLeaveSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:monitor 
                            {--health : Generate system health report}
                            {--discrepancies : Check balance discrepancies}
                            {--performance : Check performance metrics}
                            {--all : Run all monitoring checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor leave system health and performance';

    protected LeaveMonitoringService $monitoringService;

    public function __construct(LeaveMonitoringService $monitoringService)
    {
        parent::__construct();
        $this->monitoringService = $monitoringService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting leave system monitoring...');

        if ($this->option('all') || $this->option('health')) {
            $this->runHealthCheck();
        }

        if ($this->option('all') || $this->option('discrepancies')) {
            $this->checkDiscrepancies();
        }

        if ($this->option('all') || $this->option('performance')) {
            $this->checkPerformance();
        }

        if (!$this->option('health') && !$this->option('discrepancies') && !$this->option('performance') && !$this->option('all')) {
            $this->runHealthCheck(); // Default action
        }

        $this->info('Monitoring completed.');
        return 0;
    }

    /**
     * Run system health check.
     */
    private function runHealthCheck(): void
    {
        $this->info('Running system health check...');
        
        $report = $this->monitoringService->generateSystemHealthReport();
        
        $this->info("System Status: " . strtoupper($report['system_status']));
        $this->newLine();

        foreach ($report['checks'] as $checkName => $check) {
            $status = match($check['status']) {
                'pass' => '<info>PASS</info>',
                'warn' => '<comment>WARN</comment>',
                'fail' => '<error>FAIL</error>',
                default => $check['status']
            };

            $this->line("  {$checkName}: {$status} - {$check['message']}");
        }

        if ($report['system_status'] !== 'healthy') {
            $this->newLine();
            $this->warn('System requires attention. Check logs for details.');
        }
    }

    /**
     * Check balance discrepancies.
     */
    private function checkDiscrepancies(): void
    {
        $this->info('Checking balance discrepancies...');
        
        $result = $this->monitoringService->monitorBalanceDiscrepancies();
        $summary = $result['summary'];
        
        $this->info("Checked {$summary['total_employees_checked']} employees");
        $this->info("Found {$summary['employees_with_discrepancies']} employees with discrepancies");
        $this->info("Significant discrepancies: {$summary['significant_discrepancies']}");
        $this->info("Discrepancy rate: " . round($summary['discrepancy_rate'] * 100, 2) . "%");

        if ($summary['employees_with_discrepancies'] > 0) {
            $this->newLine();
            $this->warn('Discrepancies found. Top 10:');
            
            $topDiscrepancies = array_slice($result['discrepancies'], 0, 10);
            $this->table(
                ['Employee ID', 'Name', 'Discrepancy', 'Expected', 'Actual'],
                array_map(function($disc) {
                    return [
                        $disc['employee_id'],
                        $disc['employee_name'],
                        round($disc['discrepancy'], 2),
                        round($disc['expected_balance'], 2),
                        round($disc['actual_balance'], 2)
                    ];
                }, $topDiscrepancies)
            );
        }
    }

    /**
     * Check performance metrics.
     */
    private function checkPerformance(): void
    {
        $this->info('Checking performance metrics...');
        
        $metrics = $this->monitoringService->monitorPerformanceMetrics();
        
        $this->info('Database Metrics:');
        $this->line("  Average query time: {$metrics['database_metrics']['avg_query_time']}ms");
        $this->line("  Total ledger entries: {$metrics['database_metrics']['total_ledger_entries']}");
        $this->line("  Active employees: {$metrics['database_metrics']['active_employees']}");

        $this->info('Cache Metrics:');
        $this->line("  Cache response time: {$metrics['cache_metrics']['cache_response_time']}ms");
        $this->line("  Cache working: " . ($metrics['cache_metrics']['cache_working'] ? 'Yes' : 'No'));

        if (isset($metrics['processing_metrics']['last_processing_time'])) {
            $this->info('Processing Metrics:');
            $this->line("  Last processing time: {$metrics['processing_metrics']['last_processing_time']}");
            $this->line("  Last success rate: {$metrics['processing_metrics']['last_success_rate']}");
        }
    }
}