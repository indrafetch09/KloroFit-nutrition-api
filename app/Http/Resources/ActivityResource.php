<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'activity_date' => $this->activity_date->toDateString(), // Format Y-m-d
            'duration' => $this->duration_minutes, // Duration in minutes
            'duration' => $this->duration_minutes, // Duration in minutes
            'distance' => $this->distance,
            'calories_burned' => $this->calories_burned,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
