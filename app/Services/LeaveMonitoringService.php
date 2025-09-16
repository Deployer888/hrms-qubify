<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveAccrualLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaveMonitoringService
{
    /**
     * Monitor monthly accrual processing.
     */
    public function monitorMonthlyAccrualProcessing(array $results, Carbon $forMonth): void
    {
        $monthStr = $forMonth->format('Y-m');
        
        Log::info('Monthly accrual processing completed', [
            'month' => $monthStr,
            'processed_employees' => $results['processed'],
            'skipped_employees' => $results['skipped'],
            'error_employees' => $results['errors'],
            'total_amount_accrued' => $results['total_amount'],
            'processing_timestamp' => now(),
            'success_rate' => $this->calculateSuccessRate($results)
        ]);

        // Alert on high error rates
        if ($results['errors'] > 0) {
            $errorRate = $results['errors'] / ($results['processed'] + $results['skipped'] + $results['errors']);
            if ($errorRate > 0.1) { // More than 10% errors
                Log::alert('High error rate in monthly accrual processing', [
                    'month' => $monthStr,
                    'error_rate' => $errorRate,
                    'total_errors' => $results['errors'],
                    'error_employees' => array_filter($results['employees'], function($emp) {
                        return $emp['status'] === 'error';
                    })
                ]);
            }
        }

        // Alert on low processing rates
        $totalEmployees = Employee::where('is_active', 1)->count();
        $processingRate = $results['processed'] / $totalEmployees;
        if ($processingRate < 0.5) { // Less than 50% processed
            Log::warning('Low processing rate in monthly accrual', [
                'month' => $monthStr,
                'processing_rate' => $processingRate,
                'processed' => $results['processed'],
                'total_active_employees' => $totalEmployees
            ]);
        }

        // Store metrics for trending
        $this->storeProcessingMetrics($monthStr, $results);
    }

    /**
     * Monitor balance discrepancies.
     */
    public function monitorBalanceDiscrepancies(): array
    {
        $discrepancies = [];
        $significantDiscrepancies = 0;
        $totalEmployees = 0;

        Employee::where('is_active', 1)
            ->chunk(100, function ($employees) use (&$discrepancies, &$significantDiscrepancies, &$totalEmployees) {
                foreach ($employees as $employee) {
                    if (!$employee->company_doj) continue;

                    $totalEmployees++;
                    $discrepancy = $employee->getBalanceDiscrepancy();
                    
                    if (abs($discrepancy) >= 0.01) {
                        $discrepancies[] = [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'discrepancy' => $discrepancy,
                            'expected_balance' => $employee->getExpectedPaidLeaveBalance(),
                            'actual_balance' => $employee->getTotalAccruedLeave()
                        ];

                        if (abs($discrepancy) > 1.5) {
                            $significantDiscrepancies++;
                        }
                    }
                }
            });

        $summary = [
            'total_employees_checked' => $totalEmployees,
            'employees_with_discrepancies' => count($discrepancies),
            'significant_discrepancies' => $significantDiscrepancies,
            'discrepancy_rate' => $totalEmployees > 0 ? count($discrepancies) / $totalEmployees : 0,
            'checked_at' => now()
        ];

        Log::info('Balance discrepancy monitoring completed', $summary);

        // Alert on high discrepancy rates
        if ($summary['discrepancy_rate'] > 0.1) { // More than 10% have discrepancies
            Log::alert('High balance discrepancy rate detected', [
                'discrepancy_rate' => $summary['discrepancy_rate'],
                'total_discrepancies' => count($discrepancies),
                'significant_discrepancies' => $significantDiscrepancies
            ]);
        }

        return [
            'summary' => $summary,
            'discrepancies' => $discrepancies
        ];
    }

    /**
     * Monitor system performance metrics.
     */
    public function monitorPerformanceMetrics(): array
    {
        $metrics = [
            'database_metrics' => $this->getDatabaseMetrics(),
            'cache_metrics' => $this->getCacheMetrics(),
            'processing_metrics' => $this->getProcessingMetrics(),
            'checked_at' => now()
        ];

        Log::info('Performance metrics collected', $metrics);

        // Alert on performance issues
        if ($metrics['database_metrics']['avg_query_time'] > 1000) { // Over 1 second
            Log::warning('Slow database queries detected', [
                'avg_query_time_ms' => $metrics['database_metrics']['avg_query_time']
            ]);
        }

        return $metrics;
    }

    /**
     * Alert on processing failures.
     */
    public function alertOnProcessingFailure(string $operation, array $context): void
    {
        Log::error("Leave processing failure: {$operation}", array_merge($context, [
            'timestamp' => now(),
            'operation' => $operation
        ]));

        // Store failure for trending
        Cache::increment("leave_processing_failures:{$operation}:" . now()->format('Y-m-d'));
    }

    /**
     * Generate system health report.
     */
    public function generateSystemHealthReport(): array
    {
        $report = [
            'generated_at' => now(),
            'system_status' => 'healthy', // Will be updated based on checks
            'checks' => []
        ];

        // Check 1: Recent accrual processing
        $lastMonth = Carbon::now('Asia/Kolkata')->subMonth();
        $lastMonthProcessed = LeaveAccrualLedger::where('year_month', $lastMonth->format('Y-m'))
            ->where('source', 'cron')
            ->exists();

        $report['checks']['last_month_processed'] = [
            'status' => $lastMonthProcessed ? 'pass' : 'fail',
            'message' => $lastMonthProcessed 
                ? "Last month ({$lastMonth->format('Y-m')}) was processed"
                : "Last month ({$lastMonth->format('Y-m')}) was not processed",
            'last_processed_month' => $this->getLastProcessedMonth()
        ];

        // Check 2: Balance discrepancies
        $discrepancyCheck = $this->monitorBalanceDiscrepancies();
        $report['checks']['balance_discrepancies'] = [
            'status' => $discrepancyCheck['summary']['discrepancy_rate'] < 0.05 ? 'pass' : 'warn',
            'message' => "Discrepancy rate: " . round($discrepancyCheck['summary']['discrepancy_rate'] * 100, 2) . "%",
            'details' => $discrepancyCheck['summary']
        ];

        // Check 3: System performance
        $performanceMetrics = $this->monitorPerformanceMetrics();
        $report['checks']['performance'] = [
            'status' => $performanceMetrics['database_metrics']['avg_query_time'] < 500 ? 'pass' : 'warn',
            'message' => "Average query time: {$performanceMetrics['database_metrics']['avg_query_time']}ms",
            'details' => $performanceMetrics
        ];

        // Check 4: Data integrity
        $integrityIssues = $this->checkDataIntegrity();
        $report['checks']['data_integrity'] = [
            'status' => $integrityIssues['total_issues'] === 0 ? 'pass' : 'warn',
            'message' => "Found {$integrityIssues['total_issues']} data integrity issues",
            'details' => $integrityIssues
        ];

        // Determine overall system status
        $failedChecks = array_filter($report['checks'], function($check) {
            return $check['status'] === 'fail';
        });

        $warnChecks = array_filter($report['checks'], function($check) {
            return $check['status'] === 'warn';
        });

        if (count($failedChecks) > 0) {
            $report['system_status'] = 'critical';
        } elseif (count($warnChecks) > 0) {
            $report['system_status'] = 'warning';
        }

        Log::info('System health report generated', [
            'status' => $report['system_status'],
            'failed_checks' => count($failedChecks),
            'warning_checks' => count($warnChecks)
        ]);

        return $report;
    }

    /**
     * Calculate success rate from processing results.
     */
    private function calculateSuccessRate(array $results): float
    {
        $total = $results['processed'] + $results['skipped'] + $results['errors'];
        return $total > 0 ? $results['processed'] / $total : 0;
    }

    /**
     * Store processing metrics for trending.
     */
    private function storeProcessingMetrics(string $month, array $results): void
    {
        $metrics = [
            'month' => $month,
            'processed' => $results['processed'],
            'skipped' => $results['skipped'],
            'errors' => $results['errors'],
            'total_amount' => $results['total_amount'],
            'success_rate' => $this->calculateSuccessRate($results),
            'timestamp' => now()
        ];

        Cache::put("monthly_accrual_metrics:{$month}", $metrics, 86400 * 30); // Store for 30 days
    }

    /**
     * Get database performance metrics.
     */
    private function getDatabaseMetrics(): array
    {
        $startTime = microtime(true);
        
        // Test query performance
        DB::table('leave_accrual_ledger')->count();
        
        $queryTime = (microtime(true) - $startTime) * 1000;

        return [
            'avg_query_time' => round($queryTime, 2),
            'total_ledger_entries' => LeaveAccrualLedger::count(),
            'active_employees' => Employee::where('is_active', 1)->count()
        ];
    }

    /**
     * Get cache performance metrics.
     */
    private function getCacheMetrics(): array
    {
        $testKey = 'performance_test_' . time();
        $testValue = 'test_data';
        
        $startTime = microtime(true);
        Cache::put($testKey, $testValue, 60);
        $retrieved = Cache::get($testKey);
        Cache::forget($testKey);
        $cacheTime = (microtime(true) - $startTime) * 1000;

        return [
            'cache_response_time' => round($cacheTime, 2),
            'cache_working' => $retrieved === $testValue
        ];
    }

    /**
     * Get processing performance metrics.
     */
    private function getProcessingMetrics(): array
    {
        $lastMonth = Carbon::now('Asia/Kolkata')->subMonth()->format('Y-m');
        $metrics = Cache::get("monthly_accrual_metrics:{$lastMonth}");

        return $metrics ?: [
            'last_processing_time' => null,
            'last_success_rate' => null,
            'message' => 'No recent processing metrics available'
        ];
    }

    /**
     * Get the last processed month.
     */
    private function getLastProcessedMonth(): ?string
    {
        $lastEntry = LeaveAccrualLedger::where('source', 'cron')
            ->orderBy('year_month', 'desc')
            ->first();

        return $lastEntry ? $lastEntry->year_month : null;
    }

    /**
     * Check data integrity issues.
     */
    private function checkDataIntegrity(): array
    {
        $issues = [];

        // Check for employees without DOJ
        $employeesWithoutDoj = Employee::whereNull('company_doj')->count();
        if ($employeesWithoutDoj > 0) {
            $issues[] = "{$employeesWithoutDoj} employees without date of joining";
        }

        // Check for duplicate cron entries
        $duplicateCronEntries = DB::table('leave_accrual_ledger')
            ->select('employee_id', 'year_month')
            ->where('source', 'cron')
            ->groupBy('employee_id', 'year_month')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicateCronEntries > 0) {
            $issues[] = "{$duplicateCronEntries} duplicate cron entries found";
        }

        // Check for invalid amounts
        $invalidAmounts = LeaveAccrualLedger::where('amount', 0)
            ->orWhere('amount', '>', 50)
            ->orWhere('amount', '<', -50)
            ->count();

        if ($invalidAmounts > 0) {
            $issues[] = "{$invalidAmounts} entries with invalid amounts";
        }

        return [
            'total_issues' => count($issues),
            'issues' => $issues
        ];
    }
}