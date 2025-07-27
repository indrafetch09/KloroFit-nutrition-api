<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalRequest;
use App\Http\Resources\GoalResource;
use App\Services\GoalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    protected $goalService;

    public function __construct(GoalService $goalService)
    {
        $this->goalService = $goalService;
    }

    /**
     * Create or update a daily goal for the authenticated user.
     */
    public function storeOrUpdate(GoalRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::user()->id;

        $goal = $this->goalService->createOrUpdateGoal($validatedData['user_id'], $validatedData);

        return (new GoalResource($goal))->response()->setStatusCode(200);
    }

    /**
     * Get a specific goal by date for the authenticated user.
     */
    public function show(string $date, Request $request): JsonResponse
    {
        $goal = $this->goalService->getGoalByDate($request->user()->id, $date);

        if (!$goal) {
            return response()->json(['message' => 'Goal not found for this date'], 404);
        }

        return (new GoalResource($goal))->response()->setStatusCode(200);
    }

    /**
     * Delete a goal by date for the authenticated user.
     */
    public function destroy(string $date, Request $request): JsonResponse
    {
        $goal = $this->goalService->getGoalByDate($request->user()->id, $date);

        if (!$goal) {
            return response()->json(['message' => 'Goal not found for this date'], 404);
        }

        $this->goalService->deleteGoal($goal);
        return response()->json(null, 204);
    }
}
