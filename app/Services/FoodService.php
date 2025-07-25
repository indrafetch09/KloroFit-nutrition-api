<?php

use App\Models\UserFood;
use App\Models\NutritionLibrary;
use App\Services\SummaryFoodService;

class FoodService
{
    protected $summaryService;

    public function __construct(SummaryFoodService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    public function addFood($userId, $nutritionId, $quantity, $mealType, $date)
    {
        NutritionLibrary::findOrFail($nutritionId);

        // Simpan ke user_foods
        UserFood::create([
            'user_id' => $userId,
            'nutrition_id' => $nutritionId,
            'quantity' => $quantity,
            'meal_type' => $mealType,
            'date' => $date,
        ]);

        // Hitung ulang summary
        $this->summaryService->recalculateSummary($userId, $date);
    }
}
