<?php

namespace App\Http\Controllers;

use App\Enums\MealType;
use App\Models\Food;
use App\Services\SummaryFoodService;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        if ($foods->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'You have not added any foods yet',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foods fetched successfully',
            'data' => FoodResource::collection($foods),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        if (!$user->goal || $user->goal->calories <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please set your goal first.',
            ], 403);
        }

        // Validasi manual
        $validator = Validator::make($request->all(), [
            'nutrition_libraries_id' => 'required|exists:nutrition_libraries,id',
            'meal_type' => MealType::rules(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data['user_id'] = $user->id;

        $food = Food::create($data);

        // Update summary harian
        SummaryFoodService::recalculateSummary($user->id, $data['date']);

        return response()->json([
            'success' => true,
            'message' => 'Food added and summary updated.',
            'data' => $food,
        ], 201);

        $validated = $request->validated();
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 401);
        }

        $data['portion_grams'] = $data['portion_grams'] ?? 100;

        $food = Food::create([
            'user_id' => Auth::id(),
            'nutrition_libraries_id' => $validated['nutrition_library_id'],
            'meal_type' => $validated['meal_type'],
            'date' => $validated['date'],
            'portion_grams' => $validated['portion_grams'] ?? null,
        ]);

        return response()->json(['message' => 'Food added', 'data' => $food], 201);

        $food = Food::create($data);

        SummaryFoodService::recalculateSummary($user->id, $data['date']);

        return response()->json([
            'success' => true,
            'message' => 'Food added and summary updated.',
            'data' => new FoodResource($food),
        ], 201);
    }


    public function update($id, Request $request)
    {
        $food = Food::where('user_id', Auth::id())->findOrFail($id);
        $oldDate = $food->date;

        $food->update($request->validated());

        $newDate = $request->date ?? $oldDate;

        SummaryFoodService::recalculateSummary(Auth::id(), $oldDate);
        if ($oldDate !== $newDate) {
            SummaryFoodService::recalculateSummary(Auth::id(), $newDate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Food updated & summary refreshed.',
            'data' => new FoodResource($food),
        ]);
    }

    public function destroy($id)
    {
        $food = Food::where('user_id', Auth::id())->findOrFail($id);
        $date = $food->date;
        $food->delete();

        SummaryFoodService::recalculateSummary(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Food deleted & summary updated.',
        ]);
    }
}
