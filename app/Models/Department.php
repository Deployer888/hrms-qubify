<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'created_by',
    ];

    /* public function branch(){
        return $this->hasOne('App\Models\Branch','id','branch_id');
    } */

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the office that owns the department.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    /**
     * Get the designations for the department.
     */
    public function designations()
    {
        return $this->hasMany(Designation::class, 'department_id');
    }
}
