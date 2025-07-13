<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Goal;
use App\Models\Summary;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SummaryService
{
    /**
     * Update atau create summary untuk user dan tanggal tertentu
     */
    public static function updateUserSummary(int $userId, string $date): void
    {
        // Ambil semua food dengan relasi nutritionLibrary
        $foods = Food::with('nutritionLibrary')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->get();

        // Ambil semua activity
        $activities = Activity::where('user_id', $userId)
            ->where('activity_date', $date)
            ->get();

        // Log untuk debugging
        Log::info('SummaryService Debug', [
            'user_id' => $userId,
            'date' => $date,
            'foods_count' => $foods->count(),
            'activities_count' => $activities->count()
        ]);

        // Inisialisasi data summary
        $summaryData = [
            'user_id' => $userId,
            'date' => $date,
            'type' => 'food',
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

        // Proses foods
        foreach ($foods as $food) {
            if (!$food->nutritionLibrary) {
                Log::warning('NutritionLibrary not found for food', ['food_id' => $food->id]);
                continue;
            }

            // Hitung portion factor (per 100 gram)
            $portionFactor = ($food->portion_grams ?? 0) / 100;

            // Hitung nilai nutrisi berdasarkan porsi
            $calories = ($food->nutritionLibrary->calories ?? 0) * $portionFactor;
            $protein = ($food->nutritionLibrary->protein ?? 0) * $portionFactor;
            $fat = ($food->nutritionLibrary->fat ?? 0) * $portionFactor;
            $carbs = ($food->nutritionLibrary->carbs ?? 0) * $portionFactor;

            // Akumulasi total nutrisi
            $summaryData['total_calories'] += $calories;
            $summaryData['total_protein'] += $protein;
            $summaryData['total_fat'] += $fat;
            $summaryData['total_carbs'] += $carbs;

            // Akumulasi kalori per meal type
            $mealKey = $food->meal_type . '_calories';
            if (array_key_exists($mealKey, $summaryData)) {
                $summaryData[$mealKey] += $calories;
            }

            // Log detail perhitungan
            Log::info('Food calculation', [
                'food_id' => $food->id,
                'portion_grams' => $food->portion_grams,
                'portion_factor' => $portionFactor,
                'calories' => $calories,
                'meal_type' => $food->meal_type
            ]);
        }

        // Proses activities
        foreach ($activities as $activity) {
            $summaryData['activity_calories_burned'] += $activity->calories_burned ?? 0;
        }

        // Simpan atau update summary
        $summary = Summary::updateOrCreate(
            [
                'user_id' => $userId,
                'date' => $date,
                'type' => 'activity' // Tambahkan type untuk membedakan summary
            ],
            $summaryData
        );

        Log::info('Summary updated', [
            'summary_id' => $summary->id,
            'total_calories' => $summaryData['total_calories']
        ]);
    }
    /**
     * Ambil data goal dari user berdasarkan tanggal tertentu
     */
    public static function getUserGoalByDate(int $userId, string $date): ?Goal
    {
        return Goal::where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }
    /**
     * Ambil summary untuk user dan tanggal tertentu
     */
    public static function getUserSummary(int $userId, string $date): ?Summary
    {
        return Summary::where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Ambil summary dengan data detail
     */
    public static function getUserSummaryWithDetails(int $userId, string $date): array
    {
        // Update summary terlebih dahulu
        self::updateUserSummary($userId, $date);

        // Ambil summary
        $summary = self::getUserSummary($userId, $date);

        if (!$summary) {
            return [
                'success' => false,
                'message' => 'No data found for the specified date',
                'data' => null
            ];
        }

        // Ambil detail foods dan activities
        $foods = Food::with('nutritionLibrary')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->get();

        $activities = Activity::where('user_id', $userId)
            ->where('activity_date', $date)
            ->get();

        return [
            'success' => true,
            'message' => 'Summary retrieved successfully',
            'data' => [
                'date' => $summary->date,
                'total_calories' => round($summary->total_calories, 2),
                'total_protein' => round($summary->total_protein, 2),
                'total_fat' => round($summary->total_fat, 2),
                'total_carbs' => round($summary->total_carbs, 2),
                'breakfast_calories' => round($summary->breakfast_calories, 2),
                'lunch_calories' => round($summary->lunch_calories, 2),
                'dinner_calories' => round($summary->dinner_calories, 2),
                'snack_calories' => round($summary->snack_calories, 2),
                'activity_calories_burned' => round($summary->activity_calories_burned, 2),
                'net_calories' => round($summary->total_calories - $summary->activity_calories_burned, 2),
                'foods_count' => $foods->count(),
                'activities_count' => $activities->count(),
                'foods' => $foods->map(function ($food) {
                    return [
                        'id' => $food->id,
                        'name' => $food->nutritionLibrary->name ?? 'Unknown',
                        'meal_type' => $food->meal_type,
                        'portion_grams' => $food->portion_grams,
                        'calories' => $food->nutritionLibrary ?
                            round(($food->nutritionLibrary->calories * $food->portion_grams / 100), 2) : 0
                    ];
                }),
                'activities' => $activities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'duration_minutes' => $activity->duration_minutes,
                        'calories_burned' => $activity->calories_burned
                    ];
                }),

            ]
        ];
    }

    /**
     * Hapus summary untuk tanggal tertentu
     */
    public static function deleteSummary(int $userId, string $date): bool
    {
        return Summary::where('user_id', $userId)
            ->where('date', $date)
            ->delete() > 0;
    }

    /**
     * Ambil summary untuk range tanggal
     */
    public static function getUserSummaryRange(int $userId, string $startDate, string $endDate): array
    {
        $summaries = Summary::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        return [
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'summaries' => $summaries->map(function ($summary) {
                    return [
                        'date' => $summary->date,
                        'total_calories' => round($summary->total_calories, 2),
                        'total_protein' => round($summary->total_protein, 2),
                        'total_fat' => round($summary->total_fat, 2),
                        'total_carbs' => round($summary->total_carbs, 2),
                        'activity_calories_burned' => round($summary->activity_calories_burned, 2),
                        'net_calories' => round($summary->total_calories - $summary->activity_calories_burned, 2)
                    ];
                }),
                'averages' => [
                    'avg_calories' => round($summaries->avg('total_calories'), 2),
                    'avg_protein' => round($summaries->avg('total_protein'), 2),
                    'avg_fat' => round($summaries->avg('total_fat'), 2),
                    'avg_carbs' => round($summaries->avg('total_carbs'), 2),
                    'avg_activity_calories' => round($summaries->avg('activity_calories_burned'), 2)
                ],
                'totals' => [
                    'total_calories' => round($summaries->sum('total_calories'), 2),
                    'total_protein' => round($summaries->sum('total_protein'), 2),
                    'total_fat' => round($summaries->sum('total_fat'), 2),
                    'total_carbs' => round($summaries->sum('total_carbs'), 2),
                    'total_activity_calories' => round($summaries->sum('activity_calories_burned'), 2)
                ]
            ]
        ];
    }
}
