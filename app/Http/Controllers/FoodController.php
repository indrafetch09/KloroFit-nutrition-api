<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Http\Resources\FoodResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodController extends Controller
{
    // List foods
    public function summary(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'meal_Type' => 'nullable|in:breakfast,lunch,dinner,snack',


        ]);
    }

    // Add foods
    public function store(Request $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $food = Food::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Food added',
            'data' => new FoodResource($food),
        ]);
    }

    // Update foods
    public function update($id, Request $request)
    {
        $food = Food::where('user_id', Auth::id())->findOrFail($id);
        $food->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Food updated',
            'data' => $food,
        ]);
    }

    // Delete foods
    public function destroy($id)
    {
        $food = Food::where('user_id', Auth::id())->findOrFail($id);
        $food->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food deleted',
        ]);
    }
}
