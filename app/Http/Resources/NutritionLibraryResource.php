<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NutritionLibraryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'calories' => $this->calories,
            'fat' => $this->fat,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            //     'is_verified' => $this->is_verified,
            //     'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
