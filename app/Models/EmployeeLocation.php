<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'location_name',
        'latitude',
        'longitude',
        'time',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    /**
     * Scope to get locations for a specific employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to get locations within a date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('time', [$startDate, $endDate]);
    }

    /**
     * Scope to get today's locations
     */
    public function scopeToday($query)
    {
        return $query->whereDate('time', today());
    }
}