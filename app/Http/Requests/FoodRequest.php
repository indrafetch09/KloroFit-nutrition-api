<?php

namespace App\Http\Requests;

use App\Enums\FoodType;
use App\Enums\MealType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FoodRequest extends FormRequest
{
    /**
     * @property \App\Models\User $user
     */

    public function authorize(): bool
    {
        return auth('sanctum')->check(); // Pastikan user login
    }

    public function rules(): array
    {
        $common = [
            'meal_type' => ['required', Rule::in(FoodType::values())],
            'date' => ['required', 'date', 'before_or_equal:today'],
        ];

        if (request()->isMethod('POST')) {
            return array_merge($common, [
                'nutrition_library_id' => ['required', 'exists:nutrition_libraries,id'],
            ]);
        }

        if (request()->isMethod('PUT')) {
            return array_merge([
                'meal_type' => ['sometimes', Rule::in(FoodType::values())],
                'date' => ['sometimes', 'date', 'before_or_equal:today'],
                'nutrition_library_id' => ['sometimes', 'exists:nutrition_libraries,id'],
            ]);
        }

        return $common;
    }

    public function messages(): array
    {
        return [
            'meal_type.in' => 'Meal type is not valid. It must be one of these types: breakfast, lunch, dinner, or snack.',
            'date.before_or_equal' => 'Date is not valid. It must be today or earlier.',
            'nutrition_library_id.exists' => 'The selected nutrition library does not exist.',
        ];
    }
}
