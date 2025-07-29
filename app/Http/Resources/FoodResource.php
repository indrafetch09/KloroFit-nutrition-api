<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'meal_type' => $this->meal_type?->value,
            'calories' => $this->calories,
            'fat' => $this->fat,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'date' => $this->date->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'update_at' => $this->update_at->format('Y-m-d H:i:s'),
        ];
    }
}
