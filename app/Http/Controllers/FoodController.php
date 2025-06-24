<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Http\Resources\FoodResource;
use App\Http\Requests\FoodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodController extends Controller
{
    // List foods
    public function index()
    {
        $request->validate(request(), [
            'date' => 'date|nullable',
            'meal_type' => 'in:breakfast,lunch,dinner,snack|nullable',
        ]);

        $request->validate([
        'date' => 'date|nullable',
        'meal_type' => 'in:breakfast,lunch,dinner,snack|nullable',
        'search' => 'nullable|string'
    ]);

        $query = Food::with(['nutrition'])
            ->where('user_id', Auth::id());

    if ($request->filled('date')) {
        $query->where('date', $request->date);
    }

    if ($request->filled('meal_type')) {
        $query->where('meal_type', $request->meal_type);
    }

    if ($request->filled('search')) {
        $query->whereHas('nutrition', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    $foods = $query->latest()->get();

    return response()->json([
        'success' => true,
        'message' => 'Foods fetched successfully',
        'data' => FoodResource::collection($foods),
    ]);

    }      
    
    // Add foods
    public function store(FoodRequest $request)
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
    public function update($id, FoodRequest $request)
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
