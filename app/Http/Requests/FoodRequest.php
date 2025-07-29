<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\MealType;
use Illuminate\Validation\Rule; // Import Rule

class FoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nutrition_library_id' => [ // <--- Ini yang baru
                'required',
                'integer',
                'exists:nutrition_libraries,id' // Memastikan ID ada di tabel nutrition_libraries
            ],
            'meal_type' => MealType::rules(), // Pastikan aturan ini sudah ada
            'date' => 'required|date_format:Y-m-d', // Asumsi tanggal konsumsi juga dikirim
        ];
    }

    public function messages(): array
    {
        return [
            'nutrition_library_id.required' => 'ID makanan dari perpustakaan nutrisi wajib diisi.',
            'nutrition_library_id.integer' => 'ID makanan harus berupa angka bulat.',
            'nutrition_library_id.exists' => 'Makanan dengan ID tersebut tidak ditemukan di perpustakaan nutrisi.',
            'meal_type.in' => 'Tipe makanan tidak valid. Pilihan: ' . implode(', ', MealType::values()) . '.',
            'date.required' => 'Tanggal konsumsi wajib diisi.',
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD.'
        ];
    }
}
