<?php

namespace App\Services;

use App\Models\UserFood;
use App\Models\SummaryFood;
use Illuminate\Support\Facades\DB;

class SummaryFoodService
{
    public function recalculateSummary(int $userId, string $date): void
    {

        // Ambil data makanan dari data user
        $foods = UserFood::with('nutrition_libraries_id')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->get();

        // Hitung total nutrisi dari semua makanan
        $totals = [
            'calorie' => 0,
            'carb' => 0,
            'fat' => 0,
            'protein' => 0,
            'breakfast_calories' => 0,
            'lunch_calories' => 0,
            'dinner_calories' => 0,
            'snack_calories' => 0,
        ];

        foreach ($foods as $food) {
            $qtyFactor = $food->quantity / 100; // karena per 100 gram
            $totals['calorie'] += $food->nutrition->calorie * $qtyFactor;
            $totals['carb']    += $food->nutrition->carb * $qtyFactor;
            $totals['fat']     += $food->nutrition->fat * $qtyFactor;
            $totals['protein'] += $food->nutrition->protein * $qtyFactor;
        }

        // Simpan atau update ke summaries_foods
        SummaryFood::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'total_calories' => (int) round($totals['calories']),
                'total_carb'    => (int) round($totals['carb']),
                'total_fat'     => (int) round($totals['fat']),
                'total_protein' => (int) round($totals['protein']),
            ]
        );
    }
}
