<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Food extends Model
{
    protected $table = 'foods';

    protected $fillable = [
        'user_id',
        'nutrition_libraries_id',
        'meal_type',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nutritionLibrary(): BelongsTo
    {
        return $this->belongsTo(NutritionLibrary::class, 'nutrition_libraries_id');
    }

    public function getCaloriesAttribute()
    {
        return optional($this->nutritionLibrary)->calories;
    }

    public function getFatAttribute()
    {
        return optional($this->nutritionLibrary)->fat;
    }

    public function getProteinAttribute()
    {
        return optional($this->nutritionLibrary)->protein;
    }

    public function getCarbsAttribute()
    {
        return optional($this->nutritionLibrary)->carbs;
    }
}
