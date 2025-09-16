<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AadhaarDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'aadhaar_number',
        'gender',
        'date_of_birth',
        'year_of_birth',
        'mobile_hash',
        'email_hash',
        'care_of',
        'full_address',
        'house',
        'street',
        'landmark',
        'vtc',
        'subdistrict',
        'district',
        'state',
        'country',
        'pincode',
        'photo_encoded',
        'share_code',
    ];
}
