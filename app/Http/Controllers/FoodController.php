<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Services\SummaryService;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFoodRequest;
use App\Http\Requests\UpdateFoodRequest;

class FoodController extends Controller
{
    public function show()
    {
        $foods = Food::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        if ($foods->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'you have not added any foods yet',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foods fetched successfully',
            'data' => FoodResource::collection($foods),
        ]);
    }

    public function store(StoreFoodRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $user = Auth::user();
        if (!$user->goal || $user->goal->calories_per_day <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please set your goal first.'
            ], 403);
        }

        $foods = Food::create($data);

        if ($foods->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'you have not added any foods yet',
                'data' => null
            ], 404);
        }

        SummaryService::updateUserSummary(Auth::id(), $data['date']);

        return response()->json([
            'success' => true,
            'message' => 'Food added & summary updated',
            'data' => new FoodResource($foods),
            201
        ]);
    }

    public function update($id, UpdateFoodRequest $request)
    {
        $foods = Food::where('user_id', Auth::id())->findOrFail($id);
        $oldDate = $foods->date;

        $foods->update($request->validated());
        $newDate = $request->date ?? $oldDate;

        SummaryService::updateUserSummary(Auth::id(), $oldDate);

        if ($oldDate !== $newDate) {
            SummaryService::updateUserSummary(Auth::id(), $newDate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Food updated',
            'data' => new FoodResource($foods),
        ]);
    }

    public function destroy($id,)
    {
        $foods = Food::where('user_id', Auth::id())->findOrFail($id);
        $date = $foods->date;
        $foods->delete();

        SummaryService::updateUserSummary(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Food deleted & summary updated',
        ]);
    }
}
