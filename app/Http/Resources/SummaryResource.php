<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummaryResource extends JsonResource
{
    public function food(Request $request): array
    {
        return [
            'date' => $this->date->toDateString(),
            'total_calories' => round($this->total_calories, 2),
            'total_protein' => round($this->total_protein, 2),
            'total_fat' => round($this->total_fat, 2),
            'total_carbs' => round($this->total_carbs, 2),
            'breakfast_calories' => round($this->breakfast_calories, 2),
            'lunch_calories' => round($this->lunch_calories, 2),
            'dinner_calories' => round($this->dinner_calories, 2),
            'snack_calories' => round($this->snack_calories, 2),
        ];
    }

    public function activity(Request $request): array
    {
        return [
            'date' => $this->date->toDateString(),
            'activity_calories_burned' => round($this->activity_calories_burned, 2),
        ];
    }
}
