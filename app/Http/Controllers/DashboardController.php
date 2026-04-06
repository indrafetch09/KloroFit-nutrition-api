<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\FoodService;
use App\Services\GoalService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\FoodResource;
use App\Services\SummaryFoodService;

class DashboardController extends Controller
{
    protected SummaryFoodService $summaryFoodService;
    protected FoodService $foodService;
    protected GoalService $goalService;

    public function __construct(
        SummaryFoodService $summaryFoodService,
        FoodService $foodService,
        GoalService $goalService
    ) {
        $this->summaryFoodService = $summaryFoodService;
        $this->foodService = $foodService;
        $this->goalService = $goalService;
    }


    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $date = $request->query('date', Carbon::today()->toDateString());

        $summaryData = $this->summaryFoodService->getDashboardSummary($user, $date);

        $recentFoods = $this->foodService->getRecentFoodsForUser($user->id, $date);

        return response()->json([
            'date' => $date,
            'goals' => $summaryData['goals'],
            'consumed' => $summaryData['consumed'],
            'remaining' => $summaryData['remaining'],
            'status_calories' => $summaryData['status_calories'],
            'recently_food_added' => FoodResource::collection($recentFoods),
            'total_calories_burned' => 0, // Placeholder
        ]);
    }
}
