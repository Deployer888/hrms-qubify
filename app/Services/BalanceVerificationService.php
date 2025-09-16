<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveAccrualLedger;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BalanceVerificationService
{
    protected AccrualCalculator $calculator;
    protected RealTimeBalanceService $balanceService;

    public function __construct(AccrualCalculator $calculator, RealTimeBalanceService $balanceService)
    {
        $this->calculator = $calculator;
        $this->balanceService = $balanceService;
    }

    /**
     * Verify balance for a single employee.
     */
    public function verifyEmployeeBalance(Employee $employee): array
    {
        $now = Carbon::now('Asia/Kolkata');
        $adjustedDoj = $employee->adjusted_doj;
        $accrualStartMonth = $employee->accrual_start_month;
        
        // Calculate expected eligible months
        $eligibleMonths = $this->calculator->getEligibleMonths($adjustedDoj, $now);
        $expectedBalance = count($eligibleMonths) * 1.5;
        
        // Get actual data
        $actualAccrued = $employee->leaveAccrualLedger()->sum('amount');
        $breakdown = $employee->getBalanceBreakdown();
        
        // Find missing months
        $existingMonths = $employee->leaveAccrualLedger()
            ->where('source', 'cron')
            ->pluck('year_month')
            ->toArray();
        
        $missingMonths = array_diff($eligibleMonths, $existingMonths);
        
        // Calculate discrepancies
        $balanceDiscrepancy = $expectedBalance - $actualAccrued;
        $isCorrect = abs($balanceDiscrepancy) < 0.01 && empty($missingMonths);
        
        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'doj' => $employee->company_doj,
            'adjusted_doj' => $adjustedDoj,
            'accrual_start_month' => $accrualStartMonth,
            'expected_eligible_months' => count($eligibleMonths),
            'eligible_months' => $eligibleMonths,
            'expected_balance' => round($expectedBalance, 2),
            'actual_accrued' => round($actualAccrued, 2),
            'actual_taken' => $breakdown['total_taken'],
            'current_balance' => $breakdown['current_balance'],
            'available_balance' => $breakdown['available_balance'],
            'is_correct' => $isCorrect,
            'balance_discrepancy' => round($balanceDiscrepancy, 2),
            'missing_months' => $missingMonths,
            'existing_months' => $existingMonths,
            'last_verified' => now(),
        ];
    }

    /**
     * Verify balances for all employees.
     */
    public function verifyAllEmployeeBalances(): array
    {
        $employees = Employee::where('is_active', 1)->get();
        $results = [];
        $summary = [
            'total_employees' => $employees->count(),
            'correct_balances' => 0,
            'incorrect_balances' => 0,
            'total_discrepancy' => 0,
            'employees_with_missing_months' => 0,
        ];

        foreach ($employees as $employee) {
            $result = $this->verifyEmployeeBalance($employee);
            $results[] = $result;
            
            if ($result['is_correct']) {
                $summary['correct_balances']++;
            } else {
                $summary['incorrect_balances']++;
                $summary['total_discrepancy'] += abs($result['balance_discrepancy']);
                
                if (!empty($result['missing_months'])) {
                    $summary['employees_with_missing_months']++;
                }
            }
        }

        return [
            'summary' => $summary,
            'results' => $results,
            'verified_at' => now(),
        ];
    }

    /**
     * Generate detailed balance report.
     */
    public function generateBalanceReport(): array
    {
        $verification = $this->verifyAllEmployeeBalances();
        $incorrectBalances = collect($verification['results'])
            ->filter(fn($result) => !$result['is_correct']);

        return [
            'summary' => $verification['summary'],
            'incorrect_balances' => $incorrectBalances->values()->toArray(),
            'recommendations' => $this->generateRecommendations($incorrectBalances),
            'generated_at' => now(),
        ];
    }

    /**
     * Identify specific discrepancies.
     */
    public function identifyDiscrepancies(): array
    {
        $verification = $this->verifyAllEmployeeBalances();
        $discrepancies = [];

        foreach ($verification['results'] as $result) {
            if (!$result['is_correct']) {
                $discrepancy = [
                    'employee_id' => $result['employee_id'],
                    'employee_name' => $result['employee_name'],
                    'type' => [],
                    'details' => [],
                ];

                if (abs($result['balance_discrepancy']) >= 0.01) {
                    $discrepancy['type'][] = 'balance_mismatch';
                    $discrepancy['details'][] = "Expected: {$result['expected_balance']}, Actual: {$result['actual_accrued']}, Difference: {$result['balance_discrepancy']}";
                }

                if (!empty($result['missing_months'])) {
                    $discrepancy['type'][] = 'missing_accruals';
                    $discrepancy['details'][] = "Missing months: " . implode(', ', $result['missing_months']);
                }

                $discrepancies[] = $discrepancy;
            }
        }

        return $discrepancies;
    }

    /**
     * Generate recommendations for fixing discrepancies.
     */
    private function generateRecommendations(Collection $incorrectBalances): array
    {
        $recommendations = [];

        $missingAccruals = $incorrectBalances->filter(fn($result) => !empty($result['missing_months']));
        if ($missingAccruals->count() > 0) {
            $recommendations[] = [
                'type' => 'missing_accruals',
                'count' => $missingAccruals->count(),
                'action' => 'Run backfill command to add missing accrual entries',
                'command' => 'php artisan leaves:backfill-balances --fix',
            ];
        }

        $balanceMismatches = $incorrectBalances->filter(fn($result) => abs($result['balance_discrepancy']) >= 0.01);
        if ($balanceMismatches->count() > 0) {
            $recommendations[] = [
                'type' => 'balance_mismatch',
                'count' => $balanceMismatches->count(),
                'action' => 'Review and correct balance discrepancies',
                'command' => 'php artisan leaves:verify-balances --fix',
            ];
        }

        return $recommendations;
    }

    /**
     * Fix discrepancies for a specific employee.
     */
    public function fixEmployeeDiscrepancies(Employee $employee, bool $dryRun = true): array
    {
        $verification = $this->verifyEmployeeBalance($employee);
        $actions = [];

        if (!$verification['is_correct']) {
            // Fix missing accrual months
            if (!empty($verification['missing_months'])) {
                foreach ($verification['missing_months'] as $yearMonth) {
                    // Check if backfill entry already exists
                    $existingBackfill = LeaveAccrualLedger::where('employee_id', $employee->id)
                        ->where('year_month', $yearMonth)
                        ->where('source', 'backfill')
                        ->first();

                    if ($existingBackfill) {
                        $actions[] = [
                            'type' => 'skip_existing',
                            'year_month' => $yearMonth,
                            'note' => "Backfill entry already exists for {$yearMonth}",
                            'status' => 'skipped',
                        ];
                        continue;
                    }

                    $action = [
                        'type' => 'add_accrual',
                        'year_month' => $yearMonth,
                        'amount' => 1.5,
                        'source' => 'backfill',
                        'note' => "Backfill accrual for missing month {$yearMonth}",
                    ];

                    if (!$dryRun) {
                        LeaveAccrualLedger::create([
                            'employee_id' => $employee->id,
                            'year_month' => $yearMonth,
                            'amount' => 1.5,
                            'source' => 'backfill',
                            'note' => $action['note'],
                        ]);
                        $action['status'] = 'completed';
                    } else {
                        $action['status'] = 'planned';
                    }

                    $actions[] = $action;
                }
            }
        }

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'actions_taken' => $actions,
            'dry_run' => $dryRun,
            'fixed_at' => now(),
        ];
    }

    /**
     * Get employees with specific discrepancy types.
     */
    public function getEmployeesWithDiscrepancies(string $type = null): Collection
    {
        $verification = $this->verifyAllEmployeeBalances();
        $incorrectResults = collect($verification['results'])
            ->filter(fn($result) => !$result['is_correct']);

        if ($type === 'missing_months') {
            return $incorrectResults->filter(fn($result) => !empty($result['missing_months']));
        }

        if ($type === 'balance_mismatch') {
            return $incorrectResults->filter(fn($result) => abs($result['balance_discrepancy']) >= 0.01);
        }

        return $incorrectResults;
    }
}