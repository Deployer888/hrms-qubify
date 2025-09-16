<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveAccrualLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyAccrualService
{
    protected AccrualCalculator $calculator;

    public function __construct(AccrualCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Process monthly accruals for all eligible employees.
     */
    public function processMonthlyAccruals(Carbon $forMonth): array
    {
        $forMonth = $forMonth->copy()->setTimezone('Asia/Kolkata');
        $yearMonth = $this->calculator->formatMonthForStorage($forMonth);
        
        Log::info("Starting monthly accrual processing for {$yearMonth}");
        
        $results = [
            'processed' => 0,
            'skipped' => 0,
            'errors' => 0,
            'total_amount' => 0.0,
            'employees' => []
        ];

        // Get all active employees
        $employees = Employee::where('is_active', 1)->get();
        
        foreach ($employees as $employee) {
            try {
                $result = $this->processEmployeeAccrual($employee, $forMonth);
                
                if ($result['processed']) {
                    $results['processed']++;
                    $results['total_amount'] += $result['amount'];
                    $results['employees'][] = [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'amount' => $result['amount'],
                        'status' => 'processed'
                    ];
                } else {
                    $results['skipped']++;
                    $results['employees'][] = [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'reason' => $result['reason'],
                        'status' => 'skipped'
                    ];
                }
            } catch (\Exception $e) {
                $results['errors']++;
                $results['employees'][] = [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'error' => $e->getMessage(),
                    'status' => 'error'
                ];
                
                Log::error("Error processing accrual for employee {$employee->id}: " . $e->getMessage());
            }
        }

        Log::info("Monthly accrual processing completed", $results);
        
        return $results;
    }

    /**
     * Process accrual for a single employee.
     */
    public function processEmployeeAccrual(Employee $employee, Carbon $forMonth): array
    {
        $yearMonth = $this->calculator->formatMonthForStorage($forMonth);
        
        // Check if already processed
        $existingAccrual = LeaveAccrualLedger::where('employee_id', $employee->id)
            ->where('year_month', $yearMonth)
            ->where('source', 'cron')
            ->first();
            
        if ($existingAccrual) {
            return [
                'processed' => false,
                'reason' => 'Already processed for this month',
                'amount' => 0
            ];
        }

        // Check eligibility
        if (!$this->isEligibleForAccrual($employee, $forMonth)) {
            return [
                'processed' => false,
                'reason' => 'Not eligible for accrual',
                'amount' => 0
            ];
        }

        $accrualAmount = $this->calculator->calculateMonthlyAccrual();
        
        // Use database transaction
        DB::beginTransaction();
        
        try {
            // Record in ledger
            $this->recordAccrualLedger($employee, $forMonth, $accrualAmount);
            
            // Update employee balance
            $employee->increment('paid_leave_balance', $accrualAmount);
            
            DB::commit();
            
            return [
                'processed' => true,
                'amount' => $accrualAmount
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if employee is eligible for accrual.
     */
    public function isEligibleForAccrual(Employee $employee, Carbon $forMonth): bool
    {
        return $this->calculator->isEligibleForAccrual($employee, $forMonth);
    }

    /**
     * Record accrual in the ledger.
     */
    private function recordAccrualLedger(Employee $employee, Carbon $forMonth, float $amount): void
    {
        $yearMonth = $this->calculator->formatMonthForStorage($forMonth);
        
        LeaveAccrualLedger::create([
            'employee_id' => $employee->id,
            'year_month' => $yearMonth,
            'amount' => $amount,
            'source' => 'cron',
            'note' => "Monthly accrual for {$yearMonth}"
        ]);
    }

    /**
     * Get processing summary for a specific month.
     */
    public function getProcessingSummary(Carbon $forMonth): array
    {
        $yearMonth = $this->calculator->formatMonthForStorage($forMonth);
        
        $cronEntries = LeaveAccrualLedger::where('year_month', $yearMonth)
            ->where('source', 'cron')
            ->with('employee:id,name')
            ->get();
            
        return [
            'month' => $yearMonth,
            'total_employees' => $cronEntries->count(),
            'total_amount' => $cronEntries->sum('amount'),
            'entries' => $cronEntries->map(function ($entry) {
                return [
                    'employee_id' => $entry->employee_id,
                    'employee_name' => $entry->employee->name,
                    'amount' => $entry->amount,
                    'created_at' => $entry->created_at
                ];
            })
        ];
    }

    /**
     * Check if monthly processing has been completed for a given month.
     */
    public function isMonthProcessed(Carbon $forMonth): bool
    {
        $yearMonth = $this->calculator->formatMonthForStorage($forMonth);
        
        return LeaveAccrualLedger::where('year_month', $yearMonth)
            ->where('source', 'cron')
            ->exists();
    }
}