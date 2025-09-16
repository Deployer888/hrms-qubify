<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Technology
 *
 * @property $id
 * @property $name
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Technology extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    public function technology()
    {
        return $this->hasMany(TechnologyList::class);
    }
}
