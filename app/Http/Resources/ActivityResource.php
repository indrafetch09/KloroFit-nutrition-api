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
            'activity_date' => $this->activity_date ?
                (is_string($this->activity_date) ? $this->activity_date : $this->activity_date->toDateString()) : null,
            'type' => $this->type,
            'duration' => $this->duration_minutes,
            'distance' => $this->distance,
            'created_at' => $this->created_at ?
                (is_string($this->created_at) ? $this->created_at : $this->created_at->toDateTimeString()) : null,
            'calories_burned' => $this->calories_burned,

        ];
    }
}
