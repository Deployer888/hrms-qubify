<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HomeSlider
 *
 * @property $id
 * @property $heading
 * @property $sub_heading
 * @property $image
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class HomeSlider extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['heading', 'sub_heading', 'image'];


}
