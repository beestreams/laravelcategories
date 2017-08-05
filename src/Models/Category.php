<?php

namespace Beestreams\LaravelCategories\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Example model relation
     */
    // public function model()
    // {
    // 	return $this->morphedByMany(Model::class, 'categorizable');
    // }
}
