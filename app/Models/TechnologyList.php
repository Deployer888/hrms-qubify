<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;


/**
 * Class TechnologyList
 *
 * @property $id
 * @property $technology_id
 * @property $name
 * @property $icon
 * @property $description
 * @property $slug
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TechnologyList extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['technology_id', 'name', 'icon', 'description', 'slug'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        
        $this->attributes['slug'] = Str::slug($value, '_');
    }

    public function type()
    {
        return $this->belongsTo(Technology::class, 'technology_id', 'id');
    }
}
