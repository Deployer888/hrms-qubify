<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveAccrualLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataValidationService
{
    protected AccrualCalculator $calculator;

    public function __construct(AccrualCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Validate employee data consistency.
     */
    public function validateEmployeeData(Employee $employee): array
    {
        $issues = [];

        // Check if DOJ is null
        if (!$employee->company_doj) {
            $issues[] = 'Missing date of joining';
        }

        // Check if DOJ is in the future
        if ($employee->company_doj && Carbon::parse($employee->company_doj)->isFuture()) {
            $issues[] = 'Date of joining is in the future';
        }

        // Check if exit date is before DOJ
        if ($employee->date_of_exit && $employee->company_doj) {
            if (Carbon::parse($employee->date_of_exit)->lt(Carbon::parse($employee->company_doj))) {
                $issues[] = 'Exit date is before joining date';
            }
        }

        // Check for invalid status combinations
        if (!$employee->is_active && !$employee->date_of_exit) {
            $issues[] = 'Employee is inactive but has no exit date';
        }

        return $issues;
    }

    /**
     * Detect and fix missing accrual entries.
     */
    public function detectMissingAccrualEntries(Employee $employee, Carbon $fromMonth = null, Carbon $toMonth = null): array
    {
        if (!$employee->company_doj) {
            return ['error' => 'Employee has no date of joining'];
        }

        $adjustedDoj = $this->calculator->calculateAdjustedDoj(Carbon::parse($employee->company_doj));
        $accrualStartMonth = $this->calculator->calculateAccrualStartMonth($adjustedDoj);
        
        $fromMonth = $fromMonth ?: $accrualStartMonth;
        $toMonth = $toMonth ?: Carbon::now('Asia/Kolkata');

        // Get expected eligible months
        $expectedMonths = $this->calculator->getEligibleMonths($adjustedDoj, $toMonth);
        
        // Filter by from/to range
        $expectedMonths = array_filter($expectedMonths, function($month) use ($fromMonth, $toMonth) {
            $monthCarbon = Carbon::createFromFormat('Y-m', $month, 'Asia/Kolkata');
            return $monthCarbon->gte($fromMonth) && $monthCarbon->lte($toMonth);
        });

        // Get existing accrual months
        $existingMonths = $employee->leaveAccrualLedger()
            ->where('source', 'cron')
            ->whereBetween('year_month', [
                $fromMonth->format('Y-m'),
                $toMonth->format('Y-m')
            ])
            ->pluck('year_month')
            ->toArray();

        $missingMonths = array_diff($expectedMonths, $existingMonths);

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'expected_months' => $expectedMonths,
            'existing_months' => $existingMonths,
            'missing_months' => array_values($missingMonths),
            'missing_count' => count($missingMonths),
            'missing_amount' => count($missingMonths) * 1.5
        ];
    }

    /**
     * Fix missing accrual entries for an employee.
     */
    public function fixMissingAccrualEntries(Employee $employee, array $missingMonths, bool $dryRun = false): array
    {
        $results = [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'processed' => 0,
            'total_amount' => 0,
            'entries' => []
        ];

        if ($dryRun) {
            $results['dry_run'] = true;
            $results['would_process'] = count($missingMonths);
            $results['would_add_amount'] = count($missingMonths) * 1.5;
            return $results;
        }

        DB::beginTransaction();
        
        try {
            foreach ($missingMonths as $yearMonth) {
                // Check if entry already exists (safety check)
                $existing = LeaveAccrualLedger::where('employee_id', $employee->id)
                    ->where('year_month', $yearMonth)
                    ->where('source', 'backfill')
                    ->first();

                if ($existing) {
                    $results['entries'][] = [
                        'month' => $yearMonth,
                        'status' => 'skipped',
                        'reason' => 'Entry already exists'
                    ];
                    continue;
                }

                // Create backfill entry
                $entry = LeaveAccrualLedger::create([
                    'employee_id' => $employee->id,
                    'year_month' => $yearMonth,
                    'amount' => 1.5,
                    'source' => 'backfill',
                    'note' => "Backfill accrual for missing month {$yearMonth}"
                ]);

                $results['processed']++;
                $results['total_amount'] += 1.5;
                $results['entries'][] = [
                    'month' => $yearMonth,
                    'amount' => 1.5,
                    'status' => 'created',
                    'entry_id' => $entry->id
                ];

                Log::info("Created backfill accrual entry", [
                    'employee_id' => $employee->id,
                    'year_month' => $yearMonth,
                    'amount' => 1.5
                ]);
            }

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error fixing missing accrual entries", [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);
            
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Implement balance correction with audit trail.
     */
    public function correctBalance(Employee $employee, float $targetBalance, string $reason, bool $dryRun = false): array
    {
        $currentBalance = $employee->getTotalAccruedLeave();
        $difference = $targetBalance - $currentBalance;

        $result = [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'current_balance' => $currentBalance,
            'target_balance' => $targetBalance,
            'difference' => $difference,
            'correction_needed' => abs($difference) >= 0.01
        ];

        if (!$result['correction_needed']) {
            $result['status'] = 'no_correction_needed';
            return $result;
        }

        if ($dryRun) {
            $result['dry_run'] = true;
            $result['would_apply_correction'] = $difference;
            return $result;
        }

        DB::beginTransaction();
        
        try {
            // Create correction entry
            $entry = LeaveAccrualLedger::create([
                'employee_id' => $employee->id,
                'year_month' => Carbon::now('Asia/Kolkata')->format('Y-m'),
                'amount' => $difference,
                'source' => 'manual',
                'note' => "Balance correction: {$reason}. Adjusted by {$difference} days."
            ]);

            DB::commit();

            $result['status'] = 'corrected';
            $result['correction_entry_id'] = $entry->id;
            $result['new_balance'] = $targetBalance;

            Log::info("Applied balance correction", [
                'employee_id' => $employee->id,
                'difference' => $difference,
                'reason' => $reason,
                'entry_id' => $entry->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error applying balance correction", [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);
            
            $result['error'] = $e->getMessage();
            $result['status'] = 'error';
        }

        return $result;
    }

    /**
     * Validate ledger entry integrity.
     */
    public function validateLedgerIntegrity(Employee $employee): array
    {
        $issues = [];
        $ledgerEntries = $employee->leaveAccrualLedger()->orderBy('year_month')->get();

        foreach ($ledgerEntries as $entry) {
            // Check for invalid amounts
            if ($entry->amount == 0) {
                $issues[] = "Entry {$entry->id} has zero amount";
            }

            // Check for extremely large amounts (likely errors)
            if (abs($entry->amount) > 50) {
                $issues[] = "Entry {$entry->id} has unusually large amount: {$entry->amount}";
            }

            // Check for invalid year-month format
            if (!preg_match('/^\d{4}-\d{2}$/', $entry->year_month)) {
                $issues[] = "Entry {$entry->id} has invalid year_month format: {$entry->year_month}";
            }

            // Check for future months (cron entries only)
            if ($entry->source === 'cron') {
                $entryMonth = Carbon::createFromFormat('Y-m', $entry->year_month, 'Asia/Kolkata');
                if ($entryMonth->isFuture()) {
                    $issues[] = "Entry {$entry->id} is for future month: {$entry->year_month}";
                }
            }
        }

        // Check for duplicate cron entries
        $cronEntries = $ledgerEntries->where('source', 'cron')->groupBy('year_month');
        foreach ($cronEntries as $month => $entries) {
            if ($entries->count() > 1) {
                $issues[] = "Duplicate cron entries for month {$month}";
            }
        }

        return $issues;
    }

    /**
     * Batch validate all employees.
     */
    public function batchValidateEmployees(int $batchSize = 100): array
    {
        $results = [
            'total_employees' => 0,
            'employees_with_issues' => 0,
            'total_issues' => 0,
            'issue_summary' => [],
            'employees' => []
        ];

        Employee::where('is_active', 1)
            ->chunk($batchSize, function ($employees) use (&$results) {
                foreach ($employees as $employee) {
                    $results['total_employees']++;
                    
                    $dataIssues = $this->validateEmployeeData($employee);
                    $ledgerIssues = $this->validateLedgerIntegrity($employee);
                    $allIssues = array_merge($dataIssues, $ledgerIssues);

                    if (!empty($allIssues)) {
                        $results['employees_with_issues']++;
                        $results['total_issues'] += count($allIssues);
                        
                        $results['employees'][] = [
                            'id' => $employee->id,
                            'name' => $employee->name,
                            'issues' => $allIssues
                        ];

                        // Count issue types
                        foreach ($allIssues as $issue) {
                            $key = substr($issue, 0, 30) . '...';
                            $results['issue_summary'][$key] = ($results['issue_summary'][$key] ?? 0) + 1;
                        }
                    }
                }
            });

        return $results;
    }

    /**
     * Generate data integrity report.
     */
    public function generateIntegrityReport(): array
    {
        $report = [
            'generated_at' => now(),
            'summary' => [
                'total_employees' => Employee::count(),
                'active_employees' => Employee::where('is_active', 1)->count(),
                'employees_with_accruals' => Employee::whereHas('leaveAccrualLedger')->count(),
                'total_ledger_entries' => LeaveAccrualLedger::count(),
                'cron_entries' => LeaveAccrualLedger::where('source', 'cron')->count(),
                'backfill_entries' => LeaveAccrualLedger::where('source', 'backfill')->count(),
                'manual_entries' => LeaveAccrualLedger::where('source', 'manual')->count(),
            ],
            'validation_results' => $this->batchValidateEmployees(),
            'balance_statistics' => $this->getBalanceStatistics()
        ];

        return $report;
    }

    /**
     * Get balance statistics for reporting.
     */
    private function getBalanceStatistics(): array
    {
        $employees = Employee::where('is_active', 1)->get();
        $balances = [];
        $discrepancies = [];

        foreach ($employees as $employee) {
            if (!$employee->company_doj) continue;

            $actualBalance = $employee->getTotalAccruedLeave();
            $expectedBalance = $employee->getExpectedPaidLeaveBalance();
            $discrepancy = abs($expectedBalance - $actualBalance);

            $balances[] = $actualBalance;
            if ($discrepancy >= 0.01) {
                $discrepancies[] = $discrepancy;
            }
        }

        return [
            'total_employees_analyzed' => count($balances),
            'average_balance' => count($balances) > 0 ? round(array_sum($balances) / count($balances), 2) : 0,
            'min_balance' => count($balances) > 0 ? min($balances) : 0,
            'max_balance' => count($balances) > 0 ? max($balances) : 0,
            'employees_with_discrepancies' => count($discrepancies),
            'average_discrepancy' => count($discrepancies) > 0 ? round(array_sum($discrepancies) / count($discrepancies), 2) : 0,
            'max_discrepancy' => count($discrepancies) > 0 ? max($discrepancies) : 0
        ];
    }
}