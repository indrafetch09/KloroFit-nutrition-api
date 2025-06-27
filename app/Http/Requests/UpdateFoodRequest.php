<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'calories' => 'sometimes|required|numeric|min:0',
            'meal_type' => 'sometimes|required|in:breakfast,lunch,dinner,snack',
            'date' => 'sometimes|required|date',
        ];
    }
}
