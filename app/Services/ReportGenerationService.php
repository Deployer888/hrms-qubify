<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveAccrualLedger;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ReportGenerationService
{
    protected BalanceVerificationService $verificationService;
    protected DataValidationService $validationService;
    protected LeaveMonitoringService $monitoringService;

    public function __construct(
        BalanceVerificationService $verificationService,
        DataValidationService $validationService,
        LeaveMonitoringService $monitoringService
    ) {
        $this->verificationService = $verificationService;
        $this->validationService = $validationService;
        $this->monitoringService = $monitoringService;
    }

    /**
     * Generate comprehensive balance verification report.
     */
    public function generateBalanceVerificationReport(array $options = []): array
    {
        $employeeId = $options['employee_id'] ?? null;
        $includeCorrect = $options['include_correct'] ?? false;
        $format = $options['format'] ?? 'array';

        $report = [
            'generated_at' => now(),
            'report_type' => 'balance_verification',
            'parameters' => $options,
            'summary' => [
                'total_employees' => 0,
                'correct_balances' => 0,
                'incorrect_balances' => 0,
                'total_discrepancy_amount' => 0,
                'employees_needing_backfill' => 0
            ],
            'employees' => []
        ];

        $query = Employee::where('is_active', 1);
        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $query->chunk(100, function ($employees) use (&$report, $includeCorrect) {
            foreach ($employees as $employee) {
                if (!$employee->company_doj) continue;

                $verification = $this->verificationService->verifyEmployeeBalance($employee);
                $report['summary']['total_employees']++;

                if ($verification->isCorrect) {
                    $report['summary']['correct_balances']++;
                    if (!$includeCorrect) continue;
                } else {
                    $report['summary']['incorrect_balances']++;
                    $report['summary']['total_discrepancy_amount'] += abs($verification->discrepancy);
                    
                    if (!empty($verification->missingMonths)) {
                        $report['summary']['employees_needing_backfill']++;
                    }
                }

                $report['employees'][] = [
                    'employee_id' => $verification->employeeId,
                    'employee_name' => $verification->employeeName,
                    'doj' => $verification->doj->format('Y-m-d'),
                    'adjusted_doj' => $verification->adjustedDoj->format('Y-m-d'),
                    'accrual_start_month' => $verification->accrualStartMonth->format('Y-m'),
                    'expected_eligible_months' => $verification->expectedEligibleMonths,
                    'expected_balance' => $verification->expectedBalance,
                    'actual_accrued' => $verification->actualAccrued,
                    'actual_taken' => $verification->actualTaken,
                    'current_balance' => $verification->currentBalance,
                    'discrepancy' => $verification->discrepancy,
                    'is_correct' => $verification->isCorrect,
                    'missing_months' => $verification->missingMonths,
                    'issues' => $verification->discrepancies
                ];
            }
        });

        if ($format === 'csv') {
            return $this->exportReportToCsv($report, 'balance_verification');
        }

        return $report;
    }

    /**
     * Generate discrepancy report for HR review.
     */
    public function generateDiscrepancyReport(float $minimumDiscrepancy = 0.01): array
    {
        $report = [
            'generated_at' => now(),
            'report_type' => 'discrepancy_analysis',
            'minimum_discrepancy' => $minimumDiscrepancy,
            'summary' => [
                'total_employees_checked' => 0,
                'employees_with_discrepancies' => 0,
                'total_discrepancy_amount' => 0,
                'average_discrepancy' => 0,
                'max_discrepancy' => 0,
                'discrepancy_categories' => [
                    'minor' => 0,      // < 1.5 days
                    'moderate' => 0,   // 1.5 - 5 days
                    'major' => 0       // > 5 days
                ]
            ],
            'discrepancies' => []
        ];

        $discrepancies = [];

        Employee::where('is_active', 1)
            ->chunk(100, function ($employees) use (&$report, &$discrepancies, $minimumDiscrepancy) {
                foreach ($employees as $employee) {
                    if (!$employee->company_doj) continue;

                    $report['summary']['total_employees_checked']++;
                    $discrepancy = $employee->getBalanceDiscrepancy();
                    
                    if (abs($discrepancy) >= $minimumDiscrepancy) {
                        $report['summary']['employees_with_discrepancies']++;
                        $report['summary']['total_discrepancy_amount'] += abs($discrepancy);
                        
                        $absDiscrepancy = abs($discrepancy);
                        if ($absDiscrepancy < 1.5) {
                            $report['summary']['discrepancy_categories']['minor']++;
                        } elseif ($absDiscrepancy <= 5) {
                            $report['summary']['discrepancy_categories']['moderate']++;
                        } else {
                            $report['summary']['discrepancy_categories']['major']++;
                        }

                        $discrepancies[] = [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'doj' => $employee->company_doj->format('Y-m-d'),
                            'expected_balance' => $employee->getExpectedPaidLeaveBalance(),
                            'actual_balance' => $employee->getTotalAccruedLeave(),
                            'discrepancy' => $discrepancy,
                            'discrepancy_abs' => $absDiscrepancy,
                            'category' => $this->categorizeDiscrepancy($absDiscrepancy),
                            'missing_months' => $employee->getMissingAccrualMonths(),
                            'recommended_action' => $this->getRecommendedAction($discrepancy, $employee)
                        ];
                    }
                }
            });

        // Sort by absolute discrepancy (highest first)
        usort($discrepancies, function($a, $b) {
            return $b['discrepancy_abs'] <=> $a['discrepancy_abs'];
        });

        $report['discrepancies'] = $discrepancies;

        // Calculate averages
        if ($report['summary']['employees_with_discrepancies'] > 0) {
            $report['summary']['average_discrepancy'] = round(
                $report['summary']['total_discrepancy_amount'] / $report['summary']['employees_with_discrepancies'], 
                2
            );
            $report['summary']['max_discrepancy'] = max(array_column($discrepancies, 'discrepancy_abs'));
        }

        return $report;
    }

    /**
     * Generate system health and accuracy report.
     */
    public function generateSystemHealthReport(): array
    {
        $healthReport = $this->monitoringService->generateSystemHealthReport();
        $integrityReport = $this->validationService->generateIntegrityReport();
        
        return [
            'generated_at' => now(),
            'report_type' => 'system_health',
            'overall_status' => $healthReport['system_status'],
            'health_checks' => $healthReport['checks'],
            'data_integrity' => $integrityReport,
            'recommendations' => $this->generateRecommendations($healthReport, $integrityReport)
        ];
    }

    /**
     * Generate historical trend analysis.
     */
    public function generateHistoricalTrendAnalysis(int $months = 12): array
    {
        $endMonth = Carbon::now('Asia/Kolkata');
        $startMonth = $endMonth->copy()->subMonths($months - 1);
        
        $report = [
            'generated_at' => now(),
            'report_type' => 'historical_trend_analysis',
            'period' => [
                'start_month' => $startMonth->format('Y-m'),
                'end_month' => $endMonth->format('Y-m'),
                'total_months' => $months
            ],
            'monthly_data' => [],
            'trends' => []
        ];

        $currentMonth = $startMonth->copy();
        while ($currentMonth->lte($endMonth)) {
            $monthStr = $currentMonth->format('Y-m');
            
            $monthlyData = [
                'month' => $monthStr,
                'cron_entries' => LeaveAccrualLedger::where('year_month', $monthStr)
                    ->where('source', 'cron')->count(),
                'backfill_entries' => LeaveAccrualLedger::where('year_month', $monthStr)
                    ->where('source', 'backfill')->count(),
                'manual_entries' => LeaveAccrualLedger::where('year_month', $monthStr)
                    ->where('source', 'manual')->count(),
                'total_amount_accrued' => LeaveAccrualLedger::where('year_month', $monthStr)
                    ->sum('amount'),
                'active_employees' => $this->getActiveEmployeesForMonth($currentMonth)
            ];

            $monthlyData['processing_rate'] = $monthlyData['active_employees'] > 0 
                ? $monthlyData['cron_entries'] / $monthlyData['active_employees'] 
                : 0;

            $report['monthly_data'][] = $monthlyData;
            $currentMonth->addMonth();
        }

        // Calculate trends
        $report['trends'] = $this->calculateTrends($report['monthly_data']);

        return $report;
    }

    /**
     * Export report to CSV format.
     */
    public function exportReportToCsv(array $report, string $reportType): array
    {
        $filename = $reportType . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = 'reports/' . $filename;

        $csvData = $this->convertReportToCsv($report);
        Storage::put($filepath, $csvData);

        return [
            'report' => $report,
            'export' => [
                'format' => 'csv',
                'filename' => $filename,
                'filepath' => $filepath,
                'url' => Storage::url($filepath)
            ]
        ];
    }

    /**
     * Generate executive summary report.
     */
    public function generateExecutiveSummary(): array
    {
        $balanceReport = $this->generateBalanceVerificationReport(['include_correct' => false]);
        $discrepancyReport = $this->generateDiscrepancyReport();
        $healthReport = $this->generateSystemHealthReport();

        return [
            'generated_at' => now(),
            'report_type' => 'executive_summary',
            'key_metrics' => [
                'total_active_employees' => Employee::where('is_active', 1)->count(),
                'employees_with_correct_balances' => $balanceReport['summary']['correct_balances'],
                'employees_with_incorrect_balances' => $balanceReport['summary']['incorrect_balances'],
                'accuracy_rate' => $this->calculateAccuracyRate($balanceReport['summary']),
                'total_discrepancy_amount' => $discrepancyReport['summary']['total_discrepancy_amount'],
                'employees_needing_attention' => $discrepancyReport['summary']['discrepancy_categories']['major'],
                'system_health_status' => $healthReport['overall_status']
            ],
            'recommendations' => $this->generateExecutiveRecommendations($balanceReport, $discrepancyReport, $healthReport),
            'action_items' => $this->generateActionItems($balanceReport, $discrepancyReport)
        ];
    }

    /**
     * Categorize discrepancy severity.
     */
    private function categorizeDiscrepancy(float $absDiscrepancy): string
    {
        if ($absDiscrepancy < 1.5) return 'minor';
        if ($absDiscrepancy <= 5) return 'moderate';
        return 'major';
    }

    /**
     * Get recommended action for discrepancy.
     */
    private function getRecommendedAction(float $discrepancy, Employee $employee): string
    {
        $absDiscrepancy = abs($discrepancy);
        $missingMonths = $employee->getMissingAccrualMonths();

        if (!empty($missingMonths)) {
            return 'Run backfill for missing months: ' . implode(', ', $missingMonths);
        }

        if ($absDiscrepancy < 0.1) {
            return 'No action needed - within tolerance';
        }

        if ($discrepancy > 0) {
            return 'Employee has excess balance - review for manual adjustments';
        } else {
            return 'Employee has deficit - investigate and correct';
        }
    }

    /**
     * Convert report data to CSV format.
     */
    private function convertReportToCsv(array $report): string
    {
        if (empty($report['employees'])) {
            return "No data available\n";
        }

        $headers = array_keys($report['employees'][0]);
        $csv = implode(',', $headers) . "\n";

        foreach ($report['employees'] as $employee) {
            $row = [];
            foreach ($employee as $value) {
                if (is_array($value)) {
                    $row[] = '"' . implode('; ', $value) . '"';
                } else {
                    $row[] = '"' . str_replace('"', '""', $value) . '"';
                }
            }
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }

    /**
     * Calculate accuracy rate.
     */
    private function calculateAccuracyRate(array $summary): float
    {
        $total = $summary['correct_balances'] + $summary['incorrect_balances'];
        return $total > 0 ? round(($summary['correct_balances'] / $total) * 100, 2) : 0;
    }

    /**
     * Get active employees count for a specific month.
     */
    private function getActiveEmployeesForMonth(Carbon $month): int
    {
        return Employee::where('is_active', 1)
            ->where('company_doj', '<=', $month->endOfMonth())
            ->where(function($query) use ($month) {
                $query->whereNull('date_of_exit')
                    ->orWhere('date_of_exit', '>', $month->endOfMonth());
            })
            ->count();
    }

    /**
     * Calculate trends from monthly data.
     */
    private function calculateTrends(array $monthlyData): array
    {
        if (count($monthlyData) < 2) {
            return ['message' => 'Insufficient data for trend analysis'];
        }

        $processingRates = array_column($monthlyData, 'processing_rate');
        $totalAmounts = array_column($monthlyData, 'total_amount_accrued');

        return [
            'processing_rate_trend' => $this->calculateTrend($processingRates),
            'accrual_amount_trend' => $this->calculateTrend($totalAmounts),
            'average_processing_rate' => round(array_sum($processingRates) / count($processingRates), 4),
            'average_monthly_accrual' => round(array_sum($totalAmounts) / count($totalAmounts), 2)
        ];
    }

    /**
     * Calculate trend direction.
     */
    private function calculateTrend(array $values): string
    {
        if (count($values) < 2) return 'insufficient_data';
        
        $first = array_slice($values, 0, ceil(count($values) / 2));
        $second = array_slice($values, floor(count($values) / 2));
        
        $firstAvg = array_sum($first) / count($first);
        $secondAvg = array_sum($second) / count($second);
        
        $change = (($secondAvg - $firstAvg) / $firstAvg) * 100;
        
        if (abs($change) < 5) return 'stable';
        return $change > 0 ? 'increasing' : 'decreasing';
    }

    /**
     * Generate system recommendations.
     */
    private function generateRecommendations(array $healthReport, array $integrityReport): array
    {
        $recommendations = [];

        if ($healthReport['system_status'] !== 'healthy') {
            $recommendations[] = 'System requires immediate attention - check failed health checks';
        }

        if ($integrityReport['validation_results']['total_issues'] > 0) {
            $recommendations[] = 'Data integrity issues detected - run data validation and correction';
        }

        if ($integrityReport['balance_statistics']['employees_with_discrepancies'] > 0) {
            $recommendations[] = 'Balance discrepancies found - consider running backfill operations';
        }

        return $recommendations;
    }

    /**
     * Generate executive recommendations.
     */
    private function generateExecutiveRecommendations(array $balanceReport, array $discrepancyReport, array $healthReport): array
    {
        $recommendations = [];

        $accuracyRate = $this->calculateAccuracyRate($balanceReport['summary']);
        if ($accuracyRate < 95) {
            $recommendations[] = "System accuracy is {$accuracyRate}% - recommend immediate review and correction";
        }

        if ($discrepancyReport['summary']['discrepancy_categories']['major'] > 0) {
            $count = $discrepancyReport['summary']['discrepancy_categories']['major'];
            $recommendations[] = "{$count} employees have major balance discrepancies requiring immediate attention";
        }

        if ($healthReport['overall_status'] !== 'healthy') {
            $recommendations[] = 'System health issues detected - technical review required';
        }

        return $recommendations;
    }

    /**
     * Generate action items.
     */
    private function generateActionItems(array $balanceReport, array $discrepancyReport): array
    {
        $actionItems = [];

        if ($balanceReport['summary']['employees_needing_backfill'] > 0) {
            $actionItems[] = [
                'priority' => 'high',
                'action' => 'Run backfill operations',
                'description' => "Process missing accruals for {$balanceReport['summary']['employees_needing_backfill']} employees",
                'command' => 'php artisan leaves:backfill-balances'
            ];
        }

        if ($discrepancyReport['summary']['discrepancy_categories']['major'] > 0) {
            $actionItems[] = [
                'priority' => 'high',
                'action' => 'Review major discrepancies',
                'description' => "Investigate {$discrepancyReport['summary']['discrepancy_categories']['major']} employees with major balance issues",
                'command' => 'php artisan leaves:verify-balances --report'
            ];
        }

        return $actionItems;
    }
}