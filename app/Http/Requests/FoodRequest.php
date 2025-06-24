<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Jika pakai middleware auth, ini aman
    }

    public function rules(): array
    {
        return [
            'nutrition_libraries_id' => 'required|exists:nutrition_libraries,id',
            'name' => 'required|string|max:255',
            'meal_type' => MealType::rules(),
        ];
    }
}
