<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\FoodResource;
use App\Services\SummaryFoodService;
use App\Services\GoalService; // Untuk goals
use App\Services\FoodService; // Untuk recent foods

class DashboardController extends Controller
{
    protected SummaryFoodService $summaryFoodService;
    protected FoodService $foodService;
    protected GoalService $goalService; // Inject GoalService

    public function __construct(
        SummaryFoodService $summaryFoodService,
        FoodService $foodService,
        GoalService $goalService // Inject GoalService
    ) {
        $this->summaryFoodService = $summaryFoodService;
        $this->foodService = $foodService;
        $this->goalService = $goalService; // Assign GoalService
    }

    /**
     * Get all data required for the user's dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        // 1. Dapatkan Summary Makanan Hari Ini (Consumed)
        $summaryData = $this->summaryFoodService->getTodaySummaryForUser($user);

        // 2. Dapatkan Goal Hari Ini
        $goalData = $this->goalService->getGoalByDate($user->id, $today);
        $dailyGoal = [
            'calories' => $goalData ? $goalData->calories : 0,
            'carbs' => $goalData ? $goalData->carbs : 0,
            'protein' => $goalData ? $goalData->protein : 0,
            'fat' => $goalData ? $goalData->fat : 0,
        ];

        // 3. Hitung Sisa Kalori/Nutrisi (untuk tampilan seperti "390 / 2000 cal")
        $remainingCalories = $dailyGoal['calories'] - $summaryData['consumed']['calories'];
        $statusCalories = ($dailyGoal['calories'] > 0 && $remainingCalories >= 0) ? 'within_goal' : 'over_goal';

        // 4. Dapatkan Makanan yang Baru Ditambahkan (Recently Food Added)
        // Asumsi kita hanya ingin yang ditambahkan hari ini
        $recentFoods = $this->foodService->getRecentFoodsForUser($user->id, $today);

        // 5. Data Summary Activities (total calories burned)
        // Ini akan membutuhkan Service dan Model 'Activity' yang terpisah.
        // Untuk sementara, kita bisa mock data atau ambil dari service Activity jika sudah ada.
        $totalCaloriesBurned = 0; // Placeholder, Anda akan implementasi ini
        // $activitySummary = $this->activityService->getTodayActivitySummary($user);
        // $totalCaloriesBurned = $activitySummary['total_calories_burned'];


        // Gabungkan semua data untuk respons dashboard
        return response()->json([
            'date' => $today,
            'goals' => $dailyGoal,
            'consumed' => $summaryData['consumed'],
            'remaining' => [
                'calories' => $remainingCalories,
                'carbs' => $dailyGoal['carbs'] - $summaryData['consumed']['carbs'],
                'protein' => $dailyGoal['protein'] - $summaryData['consumed']['protein'],
                'fat' => $dailyGoal['fat'] - $summaryData['consumed']['fat'],
            ],
            'status_calories' => $statusCalories,
            'recently_food_added' => FoodResource::collection($recentFoods), // Gunakan FoodResource
            'total_calories_burned' => $totalCaloriesBurned, // Dari Activity Service
            // Tambahkan data Recently Activities jika ada
        ]);
    }
}
