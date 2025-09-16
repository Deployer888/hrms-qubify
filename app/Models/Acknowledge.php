<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acknowledge extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'emp_id',
        'company_policy_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function companyPolicy()
    {
        return $this->belongsTo(CompanyPolicy::class, 'company_policy_id');
    }
}
