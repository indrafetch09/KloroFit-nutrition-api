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
            'name' => $this->name,
            'calories' => $this->calories,
            'meal_type' => $this->meal_type,
            'date' => $this->date->toDateString(), // Format Y-m-d
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
