<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HomeRoadmap
 *
 * @property $id
 * @property $title
 * @property $description
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class HomeRoadmap extends Model
{
    use HasFactory;

    // Define the factory class for the model
    // protected static function newFactory()
    // {
    //     return \Database\Factories\RoadmapFactory::new();
    // }
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'home_roadmap';
    protected $fillable = ['title', 'description', 'icon'];


}
