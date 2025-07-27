<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Pastikan user login
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'calories' => 'required|numeric|min:0',
            'carbs' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
        ];
    }

    // public function messages(): array
    // {
    //     return [

    //         'date.required'     => 'Tanggal goal wajib diisi.',
    //         'date.date'         => 'Format tanggal tidak valid.',

    //         'calories.required' => 'Jumlah kalori wajib diisi.',
    //         'calories.numeric'  => 'Kalori harus berupa angka.',
    //         'calories.min'      => 'Kalori tidak boleh kurang dari 0.',

    //         'carbs.required'    => 'Jumlah karbohidrat wajib diisi.',
    //         'carbs.numeric'     => 'Karbohidrat harus berupa angka.',
    //         'carbs.min'         => 'Karbohidrat tidak boleh kurang dari 0.',

    //         'protein.required'  => 'Jumlah protein wajib diisi.',
    //         'protein.numeric'   => 'Protein harus berupa angka.',
    //         'protein.min'       => 'Protein tidak boleh kurang dari 0.',

    //         'fat.required'      => 'Jumlah lemak wajib diisi.',
    //         'fat.numeric'       => 'Lemak harus berupa angka.',
    //         'fat.min'           => 'Lemak tidak boleh kurang dari 0.',
    //     ];
    // }
}
