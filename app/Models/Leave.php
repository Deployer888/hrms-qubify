<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\TimeFormatHelper;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'Leave_type_id',
        'applied_on',
        'start_date',
        'end_date',
        'total_leave_days',
        'leave_reason',
        'remark',
        'status',
        'created_by',
        'leavetype',
        'start_time',
        'end_time',
        'day_segment',
        'reject_reason',
        'is_halfday',
    ];

    public function leaveType()
    {
        return $this->hasOne('App\Models\LeaveType', 'id', 'leave_type_id');
    }

    public function employees()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }

    /**
     * Get formatted start time for display (12-hour format)
     */
    public function getFormattedStartTimeAttribute(): string
    {
        return TimeFormatHelper::formatTimeForDisplay($this->start_time);
    }

    /**
     * Get formatted end time for display (12-hour format)
     */
    public function getFormattedEndTimeAttribute(): string
    {
        return TimeFormatHelper::formatTimeForDisplay($this->end_time);
    }

    /**
     * Get formatted time range for display
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        return TimeFormatHelper::getFormattedTimeRange($this->start_time, $this->end_time);
    }

    /**
     * Get formatted start date with time for display in Start Date column
     */
    public function getFormattedStartDateTimeAttribute(): string
    {
        if (empty($this->start_date)) {
            return '';
        }

        $date = \Carbon\Carbon::parse($this->start_date)->format('M d, Y');
        
        // For short leave, include time in h:i A format
        if ($this->isShortLeave() && !empty($this->start_time)) {
            $time = TimeFormatHelper::formatTimeForDisplay($this->start_time);
            return "{$date} {$time}";
        }
        
        return $date;
    }

    /**
     * Get calculated total days based on leave type
     */
    public function getCalculatedTotalDaysAttribute(): float
    {
        // Check different possible leave type indicators
        if ($this->isShortLeave()) {
            return 0.25;
        }
        
        if ($this->isHalfDay()) {
            return 0.5;
        }
        
        // Default to full day
        return 1.0;
    }

    /**
     * Check if this is a short leave
     */
    public function isShortLeave(): bool
    {
        return in_array(strtolower($this->leavetype), ['short leave', 'short']) || 
               $this->is_halfday === 'short';
    }

    /**
     * Check if this is a half day leave
     */
    public function isHalfDay(): bool
    {
        return in_array(strtolower($this->leavetype), ['half day', 'half']) || 
               $this->is_halfday === 'yes';
    }

    /**
     * Check if this is a full day leave
     */
    public function isFullDay(): bool
    {
        return !$this->isShortLeave() && !$this->isHalfDay();
    }

    /**
     * Get dynamic leave type display name
     */
    public function getLeaveTypeDisplayAttribute(): string
    {
        if ($this->isShortLeave()) {
            return 'Short Leave';
        }
        
        if ($this->isHalfDay()) {
            return 'Half Day';
        }
        
        return 'Full Day';
    }

    /**
     * Get dynamic duration description
     */
    public function getDurationDescriptionAttribute(): string
    {
        $days = $this->calculated_total_days;
        
        if ($days == 0.25) {
            return '0.25 Days (Short Leave)';
        }
        
        if ($days == 0.5) {
            return '0.5 Days (Half Day)';
        }
        
        return $days . ($days == 1 ? ' Day' : ' Days') . ' (Full Day)';
    }

    /**
     * Check if leave has time component
     */
    public function hasTimeComponent(): bool
    {
        return $this->isShortLeave() && !empty($this->start_time) && !empty($this->end_time);
    }

    /**
     * Get contextual date display
     */
    public function getContextualDateDisplayAttribute(): string
    {
        if ($this->isShortLeave()) {
            return \Auth::user()->dateFormat($this->start_date);
        }
        
        $startDate = \Auth::user()->dateFormat($this->start_date);
        $endDate = \Auth::user()->dateFormat($this->end_date);
        
        if ($startDate === $endDate) {
            return $startDate;
        }
        
        return "{$startDate} to {$endDate}";
    }

    /**
     * Set start time attribute with standardized format
     */
    public function setStartTimeAttribute($value): void
    {
        if (!empty($value)) {
            $standardized = TimeFormatHelper::standardizeTimeInput($value);
            $this->attributes['start_time'] = $standardized;
        } else {
            $this->attributes['start_time'] = null;
        }
    }

    /**
     * Set end time attribute with standardized format
     */
    public function setEndTimeAttribute($value): void
    {
        if (!empty($value)) {
            $standardized = TimeFormatHelper::standardizeTimeInput($value);
            $this->attributes['end_time'] = $standardized;
        } else {
            $this->attributes['end_time'] = null;
        }
    }
}
