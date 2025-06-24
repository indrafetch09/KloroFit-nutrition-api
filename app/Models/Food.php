<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $fillable = [
        'user_id', 'nutrition_libraries_id', 
        'meal_type', 'date'
    ];
    
    public function user() { return $this->belongsTo(User::class); }
    public function nutritionLibrary() { return $this->belongsTo(NutritionLibrary::class,'nutrition_libraries_id'); }
    
    // Calculate actual nutrients based on portion
    public function getCalculatedNutrientsAttribute() {
        return $this->nutritionLibraries->calculateNutrients($this->nutritionlibraries_id);
    }
    
    public function getCaloriesAttribute() {
        return $this->calculated_nutrients['calories'];
    }
    
    public function getFatAttribute() {
        return $this->calculated_nutrients['fat'];
    }
    
    public function getProteinAttribute() {
        return $this->calculated_nutrients['protein'];
    }
    
    public function getCarbsAttribute() {
        return $this->calculated_nutrients['carbs'];
    }
}

