<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    // GET /goals → ambil goal user
    public function show()
    {
        $goal = Goal::where('user_id', Auth::id())->first();

        if (!$goal) {
            return response()->json([
                'success' => false,
                'message' => 'Goal not set',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Goal fetched',
            'data' => $goal
        ]);
    }

    // POST /goals → set goal pertama kali
    public function store(Request $request)
    {
        $request->validate([
            'calories' => 'required|integer|min:0',
            'protein'  => 'required|integer|min:0',
            'fat'      => 'required|integer|min:0',
            'carbs'    => 'required|integer|min:0',
        ]);

        if (Goal::where('user_id', Auth::id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Goal already exists. Use update instead.',
            ], 409);
        }

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'calories' => $request->calories,
            'protein'  => $request->protein,
            'fat'      => $request->fat,
            'carbs'    => $request->carbs,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Goal set successfully',
            'data' => $goal
        ]);
    }

    // PUT /goals → update goal jika sudah ada
    public function update(Request $request)
    {
        $request->validate([
            'calories' => 'required|integer|min:0',
            'protein'  => 'required|integer|min:0',
            'fat'      => 'required|integer|min:0',
            'carbs'    => 'required|integer|min:0',
        ]);

        $goal = Goal::where('user_id', Auth::id())->first();

        if (!$goal) {
            return response()->json([
                'success' => false,
                'message' => 'Goal not found. Set it first.',
            ], 404);
        }

        $goal->update($request->only(['calories', 'protein', 'fat', 'carbs']));

        return response()->json([
            'success' => true,
            'message' => 'Goal updated',
            'data' => $goal
        ]);
    }
}
