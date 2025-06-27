<?php

namespace App\Http\Requests;

use App\Enums\MealType;
use Illuminate\Foundation\Http\FormRequest;

class StoreFoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Izinkan semua request (bisa kamu sesuaikan kalau mau pembatasan)
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'calories' => 'required|numeric|min:0',
            'meal_type' => MealType::rules(), // breakfast, lunch, dinner, snack
            'date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama makanan wajib diisi.',
            'calories.required' => 'Kalori harus diisi.',
            'meal_type.in' => 'Waktu makan harus salah satu dari: breakfast, lunch, dinner, snack.',
            'date.required' => 'Tanggal konsumsi harus diisi.',
        ];
    }
}
