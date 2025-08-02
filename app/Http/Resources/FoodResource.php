<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource // Atau UserFoodResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nutrition_library_id' => $this->nutrition_library_id,
            'meal_type' => $this->meal_type, // Ini akan otomatis di-cast oleh model
            'date' => $this->date,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            // Jika Anda ingin menyertakan seluruh objek nutritionLibrary di dalam response:
            'nutrition_details' => new NutritionLibraryResource($this->whenLoaded('nutritionLibrary')),
        ];
    }
}
