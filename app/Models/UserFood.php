<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFood extends Model
{
    protected $table = 'user_foods';

    protected $fillable = [
        'user_id',
        'nutrition_library_id',
        'meal_type',
        'date'
    ];

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nutrition(): BelongsTo
    {
        return $this->belongsTo(NutritionLibrary::class, 'nutrition_library_id');
    }

    public function getCaloriesAttribute()
    {
        return optional($this->nutrition)->calories;
    }

    public function getFatAttribute()
    {
        return optional($this->nutrition)->fat;
    }

    public function getProteinAttribute()
    {
        return optional($this->nutrition)->protein;
    }

    public function getCarbsAttribute()
    {
        return optional($this->nutrition)->carbs;
    }
}
