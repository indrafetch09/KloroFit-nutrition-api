<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MealType;

class UserFood extends Model
{
    use HasFactory;

    protected $table = 'user_foods';

    protected $fillable = [
        'user_id',
        'nutrition_library_id',
        'meal_type',
        'date',
    ];

    protected $casts = [
        'meal_type' => MealType::class,
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tambahkan relasi ke NutritionLibrary
    public function nutritionLibrary()
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
