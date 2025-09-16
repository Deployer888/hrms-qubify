<?php

namespace App\Services;

use App\Models\Employee;
use Carbon\Carbon;

class AccrualCalculator
{
    /**
     * Calculate the adjusted date of joining (max of actual DOJ and 2024-04-01).
     */
    public function calculateAdjustedDoj(Carbon $originalDoj): Carbon
    {
        $minDate = Carbon::parse('2024-04-01', 'Asia/Kolkata');
        return $originalDoj->gt($minDate) ? $originalDoj : $minDate;
    }

    /**
     * Calculate the accrual start date (3 months after adjusted DOJ).
     */
    public function calculateAccrualStartDate(Carbon $adjustedDoj): Carbon
    {
        return $adjustedDoj->copy()->addMonths(3);
    }

    /**
     * Get eligible months for accrual based on adjusted DOJ and employment status.
     */
    public function getEligibleMonths(Carbon $adjustedDoj, Carbon $asOfMonth, array $statusHistory = []): array
    {
        $accrualStartDate = $this->calculateAccrualStartDate($adjustedDoj);
        $eligibleMonths = [];
        
        // Start from the month of accrual start date
        $currentMonth = $accrualStartDate->copy()->startOfMonth();
        
        // Generate months up to and including the specified month
        while ($currentMonth->lte($asOfMonth->endOfMonth())) {
            $monthKey = $currentMonth->format('Y-m');
            
            // For the first month, check if the accrual start date falls within this month
            if ($currentMonth->isSameMonth($accrualStartDate)) {
                // Only include this month if the accrual start date is on or before the last day of the month
                // and if we're calculating as of a date that's after the accrual start date
                if ($accrualStartDate->lte($asOfMonth)) {
                    $eligibleMonths[] = $monthKey;
                }
            } else {
                // For subsequent months, include if we're calculating as of this month or later
                if ($currentMonth->lte($asOfMonth->endOfMonth())) {
                    $eligibleMonths[] = $monthKey;
                }
            }
            
            $currentMonth->addMonth();
        }
        
        return $eligibleMonths;
    }

    /**
     * Calculate expected balance for an employee as of a specific month.
     */
    public function calculateExpectedBalance(Employee $employee, Carbon $asOfMonth): float
    {
        $adjustedDoj = $this->calculateAdjustedDoj($employee->company_doj);
        $eligibleMonths = $this->getEligibleMonths($adjustedDoj, $asOfMonth);
        
        // Calculate expected from cron accruals only
        $expectedFromCron = count($eligibleMonths) * $this->calculateMonthlyAccrual();
        
        return round($expectedFromCron, 2);
    }

    /**
     * Get the monthly accrual amount (1.5 days).
     */
    public function calculateMonthlyAccrual(): float
    {
        return 1.5;
    }

    /**
     * Check if an employee is eligible for accrual in a specific month.
     */
    public function isEligibleForAccrual(Employee $employee, Carbon $forMonth): bool
    {
        $adjustedDoj = $this->calculateAdjustedDoj($employee->company_doj);
        $accrualStartDate = $this->calculateAccrualStartDate($adjustedDoj);
        
        // Must be after cliff period - check if the month contains or is after the accrual start date
        if ($forMonth->endOfMonth()->lt($accrualStartDate)) {
            return false;
        }
        
        // Must be active on last day of month
        if (!$employee->isActiveOnLastDayOfMonth($forMonth)) {
            return false;
        }
        
        return true;
    }

    /**
     * Calculate balance discrepancy for an employee.
     */
    public function calculateBalanceDiscrepancy(Employee $employee, Carbon $asOfMonth): float
    {
        $expectedBalance = $this->calculateExpectedBalance($employee, $asOfMonth);
        $actualBalance = $employee->paid_leave_balance;
        
        return round($expectedBalance - $actualBalance, 2);
    }

    /**
     * Get eligible months count for an employee up to a specific month.
     */
    public function getEligibleMonthsCount(Employee $employee, Carbon $asOfMonth): int
    {
        $adjustedDoj = $this->calculateAdjustedDoj($employee->company_doj);
        $eligibleMonths = $this->getEligibleMonths($adjustedDoj, $asOfMonth);
        
        return count($eligibleMonths);
    }

    /**
     * Format month for database storage (YYYY-MM format).
     */
    public function formatMonthForStorage(Carbon $month): string
    {
        return $month->format('Y-m');
    }

    /**
     * Parse month from storage format to Carbon instance.
     */
    public function parseMonthFromStorage(string $yearMonth): Carbon
    {
        return Carbon::createFromFormat('Y-m', $yearMonth, 'Asia/Kolkata')->startOfMonth();
    }
}