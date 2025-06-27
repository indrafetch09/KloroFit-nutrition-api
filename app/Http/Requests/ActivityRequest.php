<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ActivityType;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => ActivityType::rules(), // run, walk, swimming, cycling
            'date' => 'required|date',
            'hour' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:1',
            'distance_km' => 'nullable|numeric|min:0',
            'calories_burned' => 'nullable|numeric|min:0',
        ];
    }
}
