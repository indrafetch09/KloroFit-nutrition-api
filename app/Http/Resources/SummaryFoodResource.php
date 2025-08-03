<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummaryFoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->date->toDateString(),
            'total_calories' => round($this->total_calories, 2),
            'total_protein' => round($this->total_protein, 2),
            'total_fat' => round($this->total_fat, 2),
            'total_carbs' => round($this->total_carbs, 2),
        ];
    }
}
