<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveAccrualLedger;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RealTimeBalanceService
{
    /**
     * Get current paid leave balance for an employee
     * Formula: Total Accrued - Total Approved Paid Leaves
     */
    public function getCurrentBalance(Employee $employee): float
    {
        $startTime = microtime(true);
        
        $balance = $this->calculateFromLedgerAndLeaves($employee);
        
        $calculationTime = (microtime(true) - $startTime) * 1000;
        $this->logCalculationMetrics($employee, $calculationTime, 'getCurrentBalance');
        
        return round($balance, 2);
    }

    /**
     * Get detailed balance breakdown for an employee
     */
    public function getBalanceBreakdown(Employee $employee): array
    {
        $startTime = microtime(true);
        
        $totalAccrued = $this->getTotalAccruedLeave($employee);
        $totalTaken = $this->getTotalApprovedPaidLeave($employee);
        $pendingApplications = $this->getPendingPaidLeaveTotal($employee);
        $totalAvailed = $totalTaken + $pendingApplications; // Total availed = approved + pending
        $currentBalance = $totalAccrued - $totalTaken;
        $availableBalance = $currentBalance - $pendingApplications;
        
        $calculationTime = (microtime(true) - $startTime) * 1000;
        $this->logCalculationMetrics($employee, $calculationTime, 'getBalanceBreakdown');
        
        return [
            'total_accrued' => round($totalAccrued, 2),
            'total_taken' => round($totalTaken, 2),
            'total_availed' => round($totalAvailed, 2), // New field for UI display
            'pending_applications' => round($pendingApplications, 2),
            'current_balance' => round($currentBalance, 2),
            'available_balance' => round($availableBalance, 2),
            'last_calculated' => Carbon::now(),
            'calculation_time_ms' => round($calculationTime, 2)
        ];
    }

    /**
     * Get balance as of a specific date
     */
    public function getBalanceAsOfDate(Employee $employee, Carbon $date): float
    {
        $startTime = microtime(true);
        
        $balance = $this->calculateFromLedgerAndLeaves($employee, $date);
        
        $calculationTime = (microtime(true) - $startTime) * 1000;
        $this->logCalculationMetrics($employee, $calculationTime, 'getBalanceAsOfDate');
        
        return round($balance, 2);
    }

    /**
     * Get available balance for new leave applications
     * This considers pending applications that would reduce available balance
     */
    public function getAvailableBalanceForNewLeave(Employee $employee): float
    {
        $breakdown = $this->getBalanceBreakdown($employee);
        return $breakdown['available_balance'];
    }

    /**
     * Calculate balances for multiple employees efficiently
     */
    public function batchCalculateBalances(Collection $employees): array
    {
        $startTime = microtime(true);
        $employeeIds = $employees->pluck('id')->toArray();
        
        // Use optimized single query for batch calculation
        $balanceData = DB::select("
            SELECT 
                e.id as employee_id,
                COALESCE(accrual.total_accrued, 0) as total_accrued,
                COALESCE(taken.total_taken, 0) as total_taken,
                COALESCE(pending.total_pending, 0) as total_pending
            FROM employees e
            LEFT JOIN (
                SELECT employee_id, SUM(amount) as total_accrued 
                FROM leave_accrual_ledger 
                WHERE employee_id IN (" . implode(',', array_fill(0, count($employeeIds), '?')) . ")
                GROUP BY employee_id
            ) accrual ON e.id = accrual.employee_id
            LEFT JOIN (
                SELECT employee_id, SUM(total_leave_days) as total_taken 
                FROM leaves 
                WHERE employee_id IN (" . implode(',', array_fill(0, count($employeeIds), '?')) . ")
                AND leave_type_id IN (SELECT id FROM leave_types WHERE title = 'Paid Leave')
                AND status = 'Approve'
                GROUP BY employee_id
            ) taken ON e.id = taken.employee_id
            LEFT JOIN (
                SELECT employee_id, SUM(total_leave_days) as total_pending 
                FROM leaves 
                WHERE employee_id IN (" . implode(',', array_fill(0, count($employeeIds), '?')) . ")
                AND leave_type_id IN (SELECT id FROM leave_types WHERE title = 'Paid Leave')
                AND status = 'Pending'
                GROUP BY employee_id
            ) pending ON e.id = pending.employee_id
            WHERE e.id IN (" . implode(',', array_fill(0, count($employeeIds), '?')) . ")
        ", array_merge($employeeIds, $employeeIds, $employeeIds, $employeeIds));
        
        $results = [];
        foreach ($balanceData as $data) {
            $currentBalance = $data->total_accrued - $data->total_taken;
            $availableBalance = $currentBalance - $data->total_pending;
            
            $results[$data->employee_id] = [
                'total_accrued' => round($data->total_accrued, 2),
                'total_taken' => round($data->total_taken, 2),
                'pending_applications' => round($data->total_pending, 2),
                'current_balance' => round($currentBalance, 2),
                'available_balance' => round($availableBalance, 2),
            ];
        }
        
        $calculationTime = (microtime(true) - $startTime) * 1000;
        Log::info('Batch balance calculation completed', [
            'employee_count' => count($employeeIds),
            'calculation_time_ms' => round($calculationTime, 2)
        ]);
        
        return $results;
    }

    /**
     * Core calculation method that combines accrual ledger and leave records
     */
    private function calculateFromLedgerAndLeaves(Employee $employee, Carbon $asOfDate = null): float
    {
        $totalAccrued = $this->getTotalAccruedLeave($employee, $asOfDate);
        $totalTaken = $this->getTotalApprovedPaidLeave($employee, $asOfDate);
        
        return $totalAccrued - $totalTaken;
    }

    /**
     * Get total accrued leave from ledger
     */
    private function getTotalAccruedLeave(Employee $employee, Carbon $asOfDate = null): float
    {
        $query = LeaveAccrualLedger::where('employee_id', $employee->id);
        
        if ($asOfDate) {
            $query->where('created_at', '<=', $asOfDate);
        }
        
        return $query->sum('amount') ?? 0;
    }

    /**
     * Get total approved paid leave days
     */
    private function getTotalApprovedPaidLeave(Employee $employee, Carbon $asOfDate = null): float
    {
        $query = Leave::where('employee_id', $employee->id)
            ->whereHas('leaveType', function($q) {
                $q->where('title', 'Paid Leave');
            })
            ->where('status', 'Approve');
        
        if ($asOfDate) {
            $query->where('start_date', '<=', $asOfDate);
        }
        
        return $query->sum('total_leave_days') ?? 0;
    }

    /**
     * Get total pending paid leave days
     */
    private function getPendingPaidLeaveTotal(Employee $employee): float
    {
        return Leave::where('employee_id', $employee->id)
            ->whereHas('leaveType', function($q) {
                $q->where('title', 'Paid Leave');
            })
            ->where('status', 'Pending')
            ->sum('total_leave_days') ?? 0;
    }

    /**
     * Log calculation metrics for performance monitoring
     */
    private function logCalculationMetrics(Employee $employee, float $calculationTime, string $method): void
    {
        // Only log if calculation takes longer than 100ms or for debugging
        if ($calculationTime > 100 || config('app.debug')) {
            Log::info('Balance calculation performed', [
                'employee_id' => $employee->id,
                'method' => $method,
                'calculation_time_ms' => round($calculationTime, 2),
                'timestamp' => now()
            ]);
        }
        
        // Log warning if calculation is slow
        if ($calculationTime > 200) {
            Log::warning('Slow balance calculation detected', [
                'employee_id' => $employee->id,
                'method' => $method,
                'calculation_time_ms' => round($calculationTime, 2)
            ]);
        }
    }
}