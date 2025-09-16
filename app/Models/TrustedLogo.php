<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrustedLogo
 *
 * @property $id
 * @property $image
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TrustedLogo extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['image'];


}
