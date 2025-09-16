<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'package_name',
        'version'
    ];

    // Cast attributes
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope to get latest version for a package
    public function scopeLatestVersion($query, $packageName)
    {
        return $query->where('package_name', $packageName)
                    ->orderBy('created_at', 'desc')
                    ->first();
    }

    // Scope to get all versions for a package
    public function scopePackageVersions($query, $packageName)
    {
        return $query->where('package_name', $packageName)
                    ->orderBy('created_at', 'desc');
    }
}
