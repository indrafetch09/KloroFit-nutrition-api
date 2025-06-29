<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'date' => $this['summary']?->date ?? now()->toDateString(),
            'goals' => [
                'calories' => $this['goal']?->calories,
                'protein' => $this['goal']?->protein,
                'fat' => $this['goal']?->fat,
                'carbs' => $this['goal']?->carbs,
            ],
            'summary' => [
                'calories' => $this['summary']?->total_calories ?? 0,
                'protein' => $this['summary']?->total_protein ?? 0,
                'fat' => $this['summary']?->total_fat ?? 0,
                'carbs' => $this['summary']?->total_carbs ?? 0,
                'burned' => $this['summary']?->calories_burned ?? 0,
            ],
            'foods' => [
                'breakfast' => FoodResource::collection($this['foods']['breakfast'] ?? []),
                'lunch' => FoodResource::collection($this['foods']['lunch'] ?? []),
                'dinner' => FoodResource::collection($this['foods']['dinner'] ?? []),
                'snack' => FoodResource::collection($this['foods']['snack'] ?? []),
            ],
            'activities' => ActivityResource::collection($this['activities']),
        ];
    }
}
