<?php

namespace App\Http\Requests;

use App\Enums\MealType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check(); // Pastikan user login
    }

    public function rules(): array
    {
        $common = [
            'meal_type' => ['required', Rule::in(MealType::values())],
            'date' => ['required', 'date', 'before_or_equal:today'],
        ];

        if (request()->isMethod('POST')) {
            return array_merge($common, [
                'nutrition_library_id' => ['required', 'exists:nutrition_libraries,id'],
                'portion_grams' => ['nullable', 'numeric', 'min:1'],
            ]);
        }

        if (request()->isMethod('PUT')) {
            return array_merge([
                'meal_type' => ['sometimes', Rule::in(MealType::values())],
                'date' => ['sometimes', 'date', 'before_or_equal:today'],
                'nutrition_library_id' => ['sometimes', 'exists:nutrition_libraries,id'],
                'portion_grams' => ['sometimes', 'numeric', 'min:1'],
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
            'portion_grams.min' => 'Portion grams must be at least 1 gram.',
        ];
    }
}
