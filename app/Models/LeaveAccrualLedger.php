<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class LeaveAccrualLedger extends Model
{
    protected $table = 'leave_accrual_ledger';

    protected $fillable = [
        'employee_id',
        'year_month',
        'amount',
        'source',
        'note',
        'leave_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the accrual ledger entry.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the leave that this ledger entry is related to.
     */
    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    /**
     * Scope a query to only include entries for a specific employee.
     */
    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope a query to only include entries for a specific month.
     */
    public function scopeForMonth(Builder $query, string $yearMonth): Builder
    {
        return $query->where('year_month', $yearMonth);
    }

    /**
     * Scope a query to only include cron-generated entries.
     */
    public function scopeByCronSource(Builder $query): Builder
    {
        return $query->where('source', 'cron');
    }

    /**
     * Scope a query to only include backfill entries.
     */
    public function scopeByBackfillSource(Builder $query): Builder
    {
        return $query->where('source', 'backfill');
    }

    /**
     * Scope a query to only include manual entries.
     */
    public function scopeByManualSource(Builder $query): Builder
    {
        return $query->where('source', 'manual');
    }

    /**
     * Scope a query to include entries within a date range.
     */
    public function scopeInDateRange(Builder $query, string $fromMonth, string $toMonth): Builder
    {
        return $query->whereBetween('year_month', [$fromMonth, $toMonth]);
    }
}
