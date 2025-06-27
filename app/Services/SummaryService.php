<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Activity;
use App\Models\Summary;
use Illuminate\Support\Facades\DB;

class SummaryService
{
    public static function updateUserSummary(int $userId, string $date): void
    {
        // Ambil semua food dan activity di tanggal itu
        $foods = Food::with('nutritionLibrary')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->get();

        $activities = Activity::where('user_id', $userId)
            ->where('activity_date', $date)
            ->get();

        // Inisialisasi akumulasi
        $summaryData = [
            'total_calories' => 0,
            'total_protein' => 0,
            'total_fat' => 0,
            'total_carbs' => 0,
            'breakfast_calories' => 0,
            'lunch_calories' => 0,
            'dinner_calories' => 0,
            'snack_calories' => 0,
            'activity_calories_burned' => 0,
        ];

        foreach ($foods as $food) {
            $portionFactor = $food->portion_grams / 100;
            $calories = $food->nutritionLibrary->calories * $portionFactor;
            $protein = $food->nutritionLibrary->protein * $portionFactor;
            $fat = $food->nutritionLibrary->fat * $portionFactor;
            $carbs = $food->nutritionLibrary->carbs * $portionFactor;

            $summaryData['total_calories'] += $calories;
            $summaryData['total_protein'] += $protein;
            $summaryData['total_fat'] += $fat;
            $summaryData['total_carbs'] += $carbs;

            $mealKey = "{$food->meal_type}_calories";
            if (isset($summaryData[$mealKey])) {
                $summaryData[$mealKey] += $calories;
            }
        }

        foreach ($activities as $activity) {
            $summaryData['activity_calories_burned'] += $activity->calories_burned;
        }

        // Simpan atau update ke summaries
        Summary::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            $summaryData
        );
    }
}
