<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nutrition_libraries' => [
                'id' => $this->nutrition?->id,
                'name' => $this->nutrition?->name,
                'calories_per_100g' => $this->nutrition?->calories_per_100g,
                'fat_per_100g' => $this->nutrition?->fat_per_100g,
                'protein_per_100g' => $this->nutrition?->protein_per_100g,
                'carbs_per_100g' => $this->nutrition?->carbs_per_100g,
                'is_verified' => $this->nutrition?->is_verified,
            ],
            'meal_type' => $this->meal_type,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
