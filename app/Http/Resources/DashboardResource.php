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
            'summary' => new SummaryResource($this['summary']),
            'foods' => [
                'breakfast' => FoodResource::collection($this['foods']->get('breakfast', collect())),
                'lunch' => FoodResource::collection($this['foods']->get('lunch', collect())),
                'dinner' => FoodResource::collection($this['foods']->get('dinner', collect())),
                'snack' => FoodResource::collection($this['foods']->get('snack', collect())),
            ],
            'activities' => ActivityResource::collection($this['activities']),
        ];
    }
}
