<?php

namespace App\Services;

use App\Models\SummaryFood;
use App\Models\User;
use App\Models\Goal;
use App\Models\UserFood;
use Carbon\Carbon;

class SummaryFoodService
{
    /**
     * Creates a full summary for the dashboard by combining Goals and consumed food.
     * This method is intended for read-only operations for the UI.
     *
     * @param \App\Models\User $user
     * @param string $dateString
     * @return array
     */
    public function getDashboardSummary(User $user, string $dateString): array
    {
        $date = Carbon::parse($dateString);

        // 1. Ambil data goals untuk tanggal yang diberikan
        $goal = Goal::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        // 2. Ambil semua entri makanan (UserFood) untuk tanggal yang diberikan
        // Eager load nutritionLibrary untuk mendapatkan detail nutrisi
        $consumedFoods = UserFood::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->with('nutritionLibrary')
            ->get();

        // 3. Hitung total nutrisi yang dikonsumsi dari semua makanan
        $totalConsumed = [
            'calories' => 0,
            'carbs' => 0,
            'protein' => 0,
            'fat' => 0,
        ];

        foreach ($consumedFoods as $food) {
            // Pastikan relasi nutritionLibrary tersedia sebelum mengakses propertinya
            if ($food->nutritionLibrary) {
                $totalConsumed['calories'] += $food->nutritionLibrary->calories;
                $totalConsumed['carbs'] += $food->nutritionLibrary->carbs;
                $totalConsumed['protein'] += $food->nutritionLibrary->protein;
                $totalConsumed['fat'] += $food->nutritionLibrary->fat;
            }
        }

        // Initialize goals with default values (0) if none exist
        $goals = [
            'calories' => $goal ? $goal->calories : 0,
            'carbs' => $goal ? $goal->carbs : 0,
            'protein' => $goal ? $goal->protein : 0,
            'fat' => $goal ? $goal->fat : 0,
        ];

        // 4. Hitung sisa nutrisi
        $remaining = [
            'calories' => $goals['calories'] - $totalConsumed['calories'],
            'carbs' => $goals['carbs'] - $totalConsumed['carbs'],
            'protein' => $goals['protein'] - $totalConsumed['protein'],
            'fat' => $goals['fat'] - $totalConsumed['fat'],
        ];

        // 5. Tentukan status kalori
        $statusCalories = ($goals['calories'] > 0 && $remaining['calories'] >= 0) ? 'within_goal' : 'over_goal';

        return [
            'date' => $dateString,
            'goals' => $goals,
            'consumed' => $totalConsumed,
            'remaining' => $remaining,
            'status_calories' => $statusCalories,
        ];
    }
}
