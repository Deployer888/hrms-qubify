<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name','created_by'
    ];

    public function departments()
    {
        return $this->hasMany(Department::class, 'branch_id');
    }
}
