<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Services\SummaryService;
use App\Http\Requests\FoodRequest;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Auth;

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

    public function store(FoodRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $data['user_id'] = Auth::id();


        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 401);
        }

        if (!$user->goal || $user->goal->calories <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please set your goal first.'
            ], 403);
        }

        $data['user_id'] = $user->id;
        $food = Food::create($data);

        SummaryService::updateUserSummary($user->id, $data['date']);

        return response()->json([
            'success' => true,
            'message' => 'Food added and summary updated.',
            'data' => new FoodResource($food),
        ], 201);
    }

    public function update($id, FoodRequest $request)
    {
        $food = Food::where('user_id', Auth::id())->findOrFail($id);
        $oldDate = $food->date;

        $food->update($request->validated());

        $newDate = $request->date ?? $oldDate;

        SummaryService::updateUserSummary(Auth::id(), $oldDate);
        if ($oldDate !== $newDate) {
            SummaryService::updateUserSummary(Auth::id(), $newDate);
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

        SummaryService::updateUserSummary(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Food deleted & summary updated.',
        ]);
    }
}
