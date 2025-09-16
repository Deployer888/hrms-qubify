<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Industry
 *
 * @property $id
 * @property $name
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Industry extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    public function industry()
    {
        return $this->hasMany(IndustryList::class);
    }
}
