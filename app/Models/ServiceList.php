<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;


/**
 * Class ServiceList
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
class ServiceList extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['service_id', 'name', 'icon', 'description', 'slug'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        
        $this->attributes['slug'] = Str::slug($value, '_');
    }

    public function type()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
