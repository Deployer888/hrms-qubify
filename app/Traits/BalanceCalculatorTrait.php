<?php

namespace App\Traits;

use App\Models\Leave;
use App\Services\RealTimeBalanceService;

trait BalanceCalculatorTrait
{
    /**
     * Get current real-time paid leave balance.
     */
    public function getCurrentPaidLeaveBalance(): float
    {
        $balanceService = app(RealTimeBalanceService::class);
        return $balanceService->getCurrentBalance($this);
    }

    /**
     * Get detailed balance breakdown.
     */
    public function getBalanceBreakdown(): array
    {
        $balanceService = app(RealTimeBalanceService::class);
        return $balanceService->getBalanceBreakdown($this);
    }

    /**
     * Get total accrued leave from ledger (excluding leave deductions).
     */
    public function getTotalAccruedLeave(): float
    {
        return round($this->leaveAccrualLedger()
            ->where('source', '!=', 'leave_deduction')
            ->sum('amount'), 2);
    }

    /**
     * Get total taken paid leave (approved only).
     */
    public function getTotalTakenPaidLeave(): float
    {
        return round(Leave::where('employee_id', $this->id)
            ->whereHas('leaveType', function($query) {
                $query->where('title', 'Paid Leave');
            })
            ->where('status', 'Approve')
            ->sum('total_leave_days'), 2);
    }

    /**
     * Get total pending paid leave applications.
     */
    public function getPendingPaidLeaveTotal(): float
    {
        return round(Leave::where('employee_id', $this->id)
            ->whereHas('leaveType', function($query) {
                $query->where('title', 'Paid Leave');
            })
            ->where('status', 'Pending')
            ->sum('total_leave_days'), 2);
    }

    /**
     * Get available balance for new applications (excluding pending).
     */
    public function getAvailableBalance(): float
    {
        $balanceService = app(RealTimeBalanceService::class);
        return $balanceService->getAvailableBalanceForNewLeave($this);
    }

    /**
     * Check if employee has sufficient balance for a leave request.
     */
    public function hasSufficientBalance(float $requestedDays): bool
    {
        return $this->getAvailableBalance() >= $requestedDays;
    }

    /**
     * Get balance as of a specific date.
     */
    public function getBalanceAsOfDate($date): float
    {
        $balanceService = app(RealTimeBalanceService::class);
        return $balanceService->getBalanceAsOfDate($this, $date);
    }
}