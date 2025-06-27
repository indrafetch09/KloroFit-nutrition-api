<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionLibrary extends Model
{
    protected $table = 'nutrition_libraries';

    protected $fillable = [
        'name',
        'calories',
        'fat',
        'protein',
        'carbs',
        'image',
        'is_verified',
    ];

    protected $casts = [
        'calories' => 'float',
        'fat' => 'float',
        'protein' => 'float',
        'carbs' => 'float',
        'is_verified' => 'boolean',
    ];
}
