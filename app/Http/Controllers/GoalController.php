<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalRequest;
use App\Http\Resources\GoalResource;
use App\Services\GoalService;
use Illuminate\Http\JsonResponse;
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
        $validatedData['user_id'] = Auth::id();

        $goal = $this->goalService->createOrUpdateGoal($validatedData['user_id'], $validatedData);

        // Memeriksa apakah goal baru saja dibuat atau diperbarui
        $wasCreated = $goal->wasRecentlyCreated;
        $message = $wasCreated ? 'Goal berhasil dibuat.' : 'Goal berhasil diperbarui.';
        $statusCode = $wasCreated ? 201 : 200;

        return response()->json([
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Get a specific goal by date for the authenticated user.
     */
    public function show(string $date): JsonResponse
    {
        $goal = $this->goalService->getGoalByDate(Auth::id(), $date);

        if (!$goal) {
            return response()->json([
                'success' => false,
                'message' => 'Goal untuk tanggal ini tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new GoalResource($goal)
        ]);
    }

    /**
     * Delete a goal by date for the authenticated user.
     */
    public function destroy(string $date): JsonResponse
    {
        $goal = $this->goalService->getGoalByDate(Auth::id(), $date);

        if (!$goal) {
            return response()->json([
                'success' => false,
                'message' => 'Goal untuk tanggal ini tidak ditemukan.'
            ], 404);
        }

        $this->goalService->deleteGoal($goal);
        return response()->json([
            'success' => true,
            'message' => 'Goal berhasil dihapus'
        ], 200);
    }
}
