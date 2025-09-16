<?php

namespace App\Console\Commands;

use App\Services\ReportGenerationService;
use Illuminate\Console\Command;

class GenerateLeaveReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:generate-reports 
                            {--type=summary : Report type (summary, balance, discrepancy, health, trend)}
                            {--employee= : Specific employee ID for balance report}
                            {--format=table : Output format (table, json, csv)}
                            {--export : Export report to file}
                            {--months=12 : Number of months for trend analysis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate various leave system reports';

    protected ReportGenerationService $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $format = $this->option('format');
        $export = $this->option('export');

        $this->info("Generating {$type} report...");

        try {
            $report = match($type) {
                'summary' => $this->reportService->generateExecutiveSummary(),
                'balance' => $this->generateBalanceReport(),
                'discrepancy' => $this->reportService->generateDiscrepancyReport(),
                'health' => $this->reportService->generateSystemHealthReport(),
                'trend' => $this->reportService->generateHistoricalTrendAnalysis($this->option('months')),
                default => throw new \InvalidArgumentException("Unknown report type: {$type}")
            };

            $this->displayReport($report, $format);

            if ($export) {
                $this->exportReport($report, $type);
            }

        } catch (\Exception $e) {
            $this->error("Error generating report: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Generate balance report with options.
     */
    private function generateBalanceReport(): array
    {
        $options = [];
        
        if ($this->option('employee')) {
            $options['employee_id'] = $this->option('employee');
        }

        if ($this->option('format') === 'csv') {
            $options['format'] = 'csv';
        }

        return $this->reportService->generateBalanceVerificationReport($options);
    }

    /**
     * Display report based on format.
     */
    private function displayReport(array $report, string $format): void
    {
        switch ($format) {
            case 'json':
                $this->line(json_encode($report, JSON_PRETTY_PRINT));
                break;
                
            case 'csv':
                if (isset($report['export'])) {
                    $this->info("CSV report exported to: {$report['export']['filepath']}");
                } else {
                    $this->warn("CSV format not available for this report type");
                }
                break;
                
            case 'table':
            default:
                $this->displayTableFormat($report);
                break;
        }
    }

    /**
     * Display report in table format.
     */
    private function displayTableFormat(array $report): void
    {
        $this->info("Report Type: " . strtoupper($report['report_type']));
        $this->info("Generated At: " . $report['generated_at']);
        $this->newLine();

        switch ($report['report_type']) {
            case 'executive_summary':
                $this->displayExecutiveSummary($report);
                break;
                
            case 'balance_verification':
                $this->displayBalanceVerification($report);
                break;
                
            case 'discrepancy_analysis':
                $this->displayDiscrepancyAnalysis($report);
                break;
                
            case 'system_health':
                $this->displaySystemHealth($report);
                break;
                
            case 'historical_trend_analysis':
                $this->displayTrendAnalysis($report);
                break;
        }
    }

    /**
     * Display executive summary.
     */
    private function displayExecutiveSummary(array $report): void
    {
        $metrics = $report['key_metrics'];
        
        $this->info('KEY METRICS');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Active Employees', $metrics['total_active_employees']],
                ['Correct Balances', $metrics['employees_with_correct_balances']],
                ['Incorrect Balances', $metrics['employees_with_incorrect_balances']],
                ['Accuracy Rate', $metrics['accuracy_rate'] . '%'],
                ['Total Discrepancy Amount', $metrics['total_discrepancy_amount']],
                ['Employees Needing Attention', $metrics['employees_needing_attention']],
                ['System Health', strtoupper($metrics['system_health_status'])]
            ]
        );

        if (!empty($report['recommendations'])) {
            $this->newLine();
            $this->warn('RECOMMENDATIONS');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("• {$recommendation}");
            }
        }

        if (!empty($report['action_items'])) {
            $this->newLine();
            $this->error('ACTION ITEMS');
            foreach ($report['action_items'] as $item) {
                $priority = strtoupper($item['priority']);
                $this->line("• [{$priority}] {$item['action']}: {$item['description']}");
                if (isset($item['command'])) {
                    $this->line("  Command: {$item['command']}");
                }
            }
        }
    }

    /**
     * Display balance verification report.
     */
    private function displayBalanceVerification(array $report): void
    {
        $summary = $report['summary'];
        
        $this->info('SUMMARY');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Employees', $summary['total_employees']],
                ['Correct Balances', $summary['correct_balances']],
                ['Incorrect Balances', $summary['incorrect_balances']],
                ['Total Discrepancy Amount', $summary['total_discrepancy_amount']],
                ['Employees Needing Backfill', $summary['employees_needing_backfill']]
            ]
        );

        if (!empty($report['employees'])) {
            $this->newLine();
            $this->info('EMPLOYEE DETAILS (First 10)');
            
            $employees = array_slice($report['employees'], 0, 10);
            $this->table(
                ['ID', 'Name', 'Expected', 'Actual', 'Discrepancy', 'Status'],
                array_map(function($emp) {
                    return [
                        $emp['employee_id'],
                        $emp['employee_name'],
                        $emp['expected_balance'],
                        $emp['actual_accrued'],
                        $emp['discrepancy'],
                        $emp['is_correct'] ? 'OK' : 'ISSUE'
                    ];
                }, $employees)
            );
        }
    }

    /**
     * Display discrepancy analysis.
     */
    private function displayDiscrepancyAnalysis(array $report): void
    {
        $summary = $report['summary'];
        
        $this->info('DISCREPANCY SUMMARY');
        $this->table(
            ['Category', 'Count'],
            [
                ['Total Employees Checked', $summary['total_employees_checked']],
                ['Employees with Discrepancies', $summary['employees_with_discrepancies']],
                ['Minor Discrepancies', $summary['discrepancy_categories']['minor']],
                ['Moderate Discrepancies', $summary['discrepancy_categories']['moderate']],
                ['Major Discrepancies', $summary['discrepancy_categories']['major']],
                ['Average Discrepancy', $summary['average_discrepancy']],
                ['Maximum Discrepancy', $summary['max_discrepancy']]
            ]
        );

        if (!empty($report['discrepancies'])) {
            $this->newLine();
            $this->warn('TOP DISCREPANCIES');
            
            $topDiscrepancies = array_slice($report['discrepancies'], 0, 10);
            $this->table(
                ['ID', 'Name', 'Expected', 'Actual', 'Discrepancy', 'Category', 'Action'],
                array_map(function($disc) {
                    return [
                        $disc['employee_id'],
                        $disc['employee_name'],
                        $disc['expected_balance'],
                        $disc['actual_balance'],
                        $disc['discrepancy'],
                        strtoupper($disc['category']),
                        $disc['recommended_action']
                    ];
                }, $topDiscrepancies)
            );
        }
    }

    /**
     * Display system health report.
     */
    private function displaySystemHealth(array $report): void
    {
        $status = strtoupper($report['overall_status']);
        $this->info("OVERALL STATUS: {$status}");
        $this->newLine();

        $this->info('HEALTH CHECKS');
        foreach ($report['health_checks'] as $checkName => $check) {
            $status = strtoupper($check['status']);
            $statusColor = match($check['status']) {
                'pass' => 'info',
                'warn' => 'comment',
                'fail' => 'error',
                default => 'line'
            };
            
            $this->$statusColor("• {$checkName}: {$status} - {$check['message']}");
        }

        if (!empty($report['recommendations'])) {
            $this->newLine();
            $this->warn('RECOMMENDATIONS');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("• {$recommendation}");
            }
        }
    }

    /**
     * Display trend analysis.
     */
    private function displayTrendAnalysis(array $report): void
    {
        $period = $report['period'];
        $this->info("ANALYSIS PERIOD: {$period['start_month']} to {$period['end_month']} ({$period['total_months']} months)");
        $this->newLine();

        if (isset($report['trends']['average_processing_rate'])) {
            $trends = $report['trends'];
            $this->info('TREND SUMMARY');
            $this->table(
                ['Metric', 'Value', 'Trend'],
                [
                    ['Average Processing Rate', round($trends['average_processing_rate'] * 100, 2) . '%', strtoupper($trends['processing_rate_trend'])],
                    ['Average Monthly Accrual', $trends['average_monthly_accrual'], strtoupper($trends['accrual_amount_trend'])]
                ]
            );
        }

        $this->newLine();
        $this->info('MONTHLY DATA (Last 6 months)');
        $recentData = array_slice($report['monthly_data'], -6);
        $this->table(
            ['Month', 'Cron Entries', 'Processing Rate', 'Total Accrued'],
            array_map(function($data) {
                return [
                    $data['month'],
                    $data['cron_entries'],
                    round($data['processing_rate'] * 100, 2) . '%',
                    $data['total_amount_accrued']
                ];
            }, $recentData)
        );
    }

    /**
     * Export report to file.
     */
    private function exportReport(array $report, string $type): void
    {
        if (isset($report['export'])) {
            $this->info("Report already exported to: {$report['export']['filepath']}");
            return;
        }

        $exportResult = $this->reportService->exportReportToCsv($report, $type);
        $this->info("Report exported to: {$exportResult['export']['filepath']}");
    }
}