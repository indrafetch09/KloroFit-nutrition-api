<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'date' => $this->date->format('Y-m-d'),
            'calories' => $this->calories,
            'carbs' => $this->carbs,
            'protein' => $this->protein,
            'fat' => $this->fat,
        ];
    }
}
