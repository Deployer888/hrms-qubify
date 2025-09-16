<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveAccrualLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillService
{
    protected AccrualCalculator $calculator;

    public function __construct(AccrualCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Backfill accruals for a single employee.
     */
    public function backfillEmployee(Employee $employee, Carbon $fromMonth = null, Carbon $toMonth = null): array
    {
        $fromMonth = $fromMonth ?: Carbon::parse('2024-04-01', 'Asia/Kolkata');
        // Default to previous month to avoid including current month
        $toMonth = $toMonth ?: Carbon::now('Asia/Kolkata')->subMonth();
        
        Log::info("Starting backfill for employee {$employee->id} from {$fromMonth->format('Y-m')} to {$toMonth->format('Y-m')}");
        
        $discrepancy = $this->calculateBalanceDiscrepancy($employee, $toMonth);
        
        if (abs($discrepancy) < 0.01) {
            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'discrepancy' => 0,
                'correction_applied' => false,
                'reason' => 'Balance is already correct'
            ];
        }

        DB::beginTransaction();
        
        try {
            $this->applyBalanceCorrection(
                $employee, 
                $discrepancy, 
                "Backfill correction for period {$fromMonth->format('Y-m')} to {$toMonth->format('Y-m')}"
            );
            
            DB::commit();
            
            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'discrepancy' => $discrepancy,
                'correction_applied' => true,
                'new_balance' => $employee->fresh()->paid_leave_balance
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error during backfill for employee {$employee->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate balance discrepancy for an employee.
     */
    public function calculateBalanceDiscrepancy(Employee $employee, Carbon $asOfMonth): float
    {
        // Calculate expected balance including the current month
        $eligibleMonthsCount = $this->calculator->getEligibleMonthsCount($employee, $asOfMonth);
        $expectedFromCron = $eligibleMonthsCount * $this->calculator->calculateMonthlyAccrual();
        
        // Get actual cron accruals from ledger
        $actualCronAccruals = $employee->leaveAccrualLedger()
            ->where('source', 'cron')
            ->sum('amount');
            
        return round($expectedFromCron - $actualCronAccruals, 2);
    }

    /**
     * Apply balance correction to an employee.
     */
    public function applyBalanceCorrection(Employee $employee, float $delta, string $note): void
    {
        if (abs($delta) < 0.01) {
            return; // No correction needed
        }

        $currentMonth = Carbon::now('Asia/Kolkata');
        $yearMonth = $this->calculator->formatMonthForStorage($currentMonth);
        
        // Record in ledger
        LeaveAccrualLedger::create([
            'employee_id' => $employee->id,
            'year_month' => $yearMonth,
            'amount' => $delta,
            'source' => 'backfill',
            'note' => $note
        ]);
        
        // Update employee balance
        $employee->increment('paid_leave_balance', $delta);
        
        Log::info("Applied correction of {$delta} to employee {$employee->id}");
    }

    /**
     * Batch backfill employees with chunked processing.
     */
    public function batchBackfillEmployees(int $batchSize = 200, Carbon $fromMonth = null, Carbon $toMonth = null): array
    {
        $fromMonth = $fromMonth ?: Carbon::parse('2024-04-01', 'Asia/Kolkata');
        $toMonth = $toMonth ?: Carbon::now('Asia/Kolkata');
        
        Log::info("Starting batch backfill processing with batch size {$batchSize}");
        
        $results = [
            'total_processed' => 0,
            'corrections_applied' => 0,
            'total_correction_amount' => 0.0,
            'errors' => 0,
            'employees' => []
        ];

        Employee::where('is_active', 1)
            ->chunk($batchSize, function ($employees) use (&$results, $fromMonth, $toMonth) {
                foreach ($employees as $employee) {
                    try {
                        $result = $this->backfillEmployee($employee, $fromMonth, $toMonth);
                        
                        $results['total_processed']++;
                        $results['employees'][] = $result;
                        
                        if ($result['correction_applied']) {
                            $results['corrections_applied']++;
                            $results['total_correction_amount'] += $result['discrepancy'];
                        }
                        
                    } catch (\Exception $e) {
                        $results['errors']++;
                        $results['employees'][] = [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'error' => $e->getMessage(),
                            'correction_applied' => false
                        ];
                    }
                }
            });

        Log::info("Batch backfill processing completed", [
            'total_processed' => $results['total_processed'],
            'corrections_applied' => $results['corrections_applied'],
            'errors' => $results['errors']
        ]);
        
        return $results;
    }

    /**
     * Get detailed balance analysis for an employee.
     */
    public function getEmployeeBalanceAnalysis(Employee $employee, Carbon $asOfMonth = null): array
    {
        $asOfMonth = $asOfMonth ?: Carbon::now('Asia/Kolkata');
        
        // Get actual accruals from ledger
        $cronAccruals = $employee->leaveAccrualLedger()
            ->where('source', 'cron')
            ->sum('amount');
            
        $manualAdjustments = $employee->leaveAccrualLedger()
            ->whereIn('source', ['backfill', 'manual'])
            ->sum('amount');

        // Calculate expected based on eligible months
        $eligibleMonthsCount = $this->calculator->getEligibleMonthsCount($employee, $asOfMonth);
        $expectedFromCron = $eligibleMonthsCount * $this->calculator->calculateMonthlyAccrual();
        
        // Check if current month has been processed
        $currentMonthProcessed = $employee->leaveAccrualLedger()
            ->where('source', 'cron')
            ->where('year_month', $asOfMonth->format('Y-m'))
            ->exists();
            
        // If current month is processed but not included in eligible months, add it
        if ($currentMonthProcessed && $cronAccruals > $expectedFromCron) {
            $expectedFromCron = $cronAccruals;
            $eligibleMonthsCount = $cronAccruals / $this->calculator->calculateMonthlyAccrual();
        }

        $actualBalance = $employee->paid_leave_balance;
        $baseActualBalance = $actualBalance - $manualAdjustments;
        $discrepancy = $expectedFromCron - $baseActualBalance;

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'adjusted_doj' => $employee->adjusted_doj->format('Y-m-d'),
            'accrual_start_month' => $employee->accrual_start_month->format('Y-m'),
            'eligible_months_count' => $eligibleMonthsCount,
            'expected_from_cron' => $expectedFromCron,
            'actual_cron_accruals' => $cronAccruals,
            'manual_adjustments' => $manualAdjustments,
            'expected_total_balance' => $expectedFromCron,
            'actual_balance' => $actualBalance,
            'discrepancy' => $discrepancy,
            'needs_correction' => abs($discrepancy) >= 0.01
        ];
    }

    /**
     * Generate backfill report for all employees.
     */
    public function generateBackfillReport(Carbon $asOfMonth = null): array
    {
        $asOfMonth = $asOfMonth ?: Carbon::now('Asia/Kolkata')->endOfMonth();
        
        $employees = Employee::where('is_active', 1)->get();
        $report = [
            'as_of_month' => $asOfMonth->format('Y-m'),
            'total_employees' => $employees->count(),
            'employees_needing_correction' => 0,
            'total_discrepancy' => 0.0,
            'employees' => []
        ];

        foreach ($employees as $employee) {
            $analysis = $this->getEmployeeBalanceAnalysis($employee, $asOfMonth);
            $report['employees'][] = $analysis;
            
            if ($analysis['needs_correction']) {
                $report['employees_needing_correction']++;
                $report['total_discrepancy'] += $analysis['discrepancy'];
            }
        }

        return $report;
    }

    /**
     * Rollback backfill corrections for an employee.
     */
    public function rollbackBackfillCorrections(Employee $employee, Carbon $fromDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::today('Asia/Kolkata');
        
        $backfillEntries = $employee->leaveAccrualLedger()
            ->where('source', 'backfill')
            ->where('created_at', '>=', $fromDate)
            ->get();
            
        if ($backfillEntries->isEmpty()) {
            return [
                'employee_id' => $employee->id,
                'rollback_applied' => false,
                'reason' => 'No backfill entries found to rollback'
            ];
        }

        DB::beginTransaction();
        
        try {
            $totalRollback = 0;
            
            foreach ($backfillEntries as $entry) {
                // Create reverse entry
                LeaveAccrualLedger::create([
                    'employee_id' => $employee->id,
                    'year_month' => Carbon::now('Asia/Kolkata')->format('Y-m'),
                    'amount' => -$entry->amount,
                    'source' => 'manual',
                    'note' => "Rollback of backfill entry ID {$entry->id}"
                ]);
                
                $totalRollback += $entry->amount;
            }
            
            // Update employee balance
            $employee->decrement('paid_leave_balance', $totalRollback);
            
            DB::commit();
            
            return [
                'employee_id' => $employee->id,
                'rollback_applied' => true,
                'entries_rolled_back' => $backfillEntries->count(),
                'total_rollback_amount' => $totalRollback,
                'new_balance' => $employee->fresh()->paid_leave_balance
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}