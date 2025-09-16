<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'phone',
        'email',
        'created_by'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class)->where('is_active', 1);
    }

    public static function getOfficeById($office_id)
    {
        return self::where('id', $office_id)->first();
    }

    public static function getOfficesByCreator($created_by)
    {
        return self::where('created_by', $created_by)->get();
    }
}