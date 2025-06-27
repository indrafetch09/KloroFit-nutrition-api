<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\NutritionLibraryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nutrition_library' => new NutritionLibraryResource($this->nutritionLibrary),
            'meal_type' => $this->meal_type,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
