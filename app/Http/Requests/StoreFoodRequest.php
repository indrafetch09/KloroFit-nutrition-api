<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Pastikan body request adalah array
            'foods' => 'required|array',

            // Validasi setiap item di dalam array 'foods'
            'foods.*.user_id' => 'required|integer|exists:users,id',
            'foods.*.nutrition_library_id' => 'required|integer|exists:nutrition_libraries,id',
            'foods.*.meal_type' => 'required|string', // Pastikan Enums juga divalidasi
            'foods.*.date' => 'required|date',
            // Jika ada field lain, tambahkan di sini
        ];
    }
}
