<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ActivityType;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check(); // Pastikan user login
    }

    public function rules(): array
    {
        $common = [
            'type' => ['required', Rule::in(ActivityType::values())],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'hour' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'distance' => ['nullable', 'numeric', 'min:0'],
            'calories_burned' => ['nullable', 'numeric', 'min:0'],
        ];

        if (request()->isMethod('POST')) {
            return array_merge($common, [
                'name' => ['required', 'string', 'max:100'],
            ]);
        }

        if (request()->isMethod('PUT')) {
            return array_merge($common, [
                'name' => ['sometimes', 'string', 'max:100'],
            ]);
        }

        return $common;
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Activity type is not valid. It must be one of this type: run, walk, swimming, or cycling.',
            'date.before_or_equal' => 'Date is not valid.',
            // dst
        ];
    }
}
