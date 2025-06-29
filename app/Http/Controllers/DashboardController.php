<?php

namespace App\Http\Controllers;

use App\Models\Summary;
use App\Models\Goal;
use App\Models\Food;
use App\Models\Activity;
use App\Http\Resources\DashboardResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $userId = Auth::id();
        $date = $request->query('date', now()->toDateString());

        $summary = Summary::where('user_id', $userId)->where('date', $date)->first();
        $goal = Goal::where('user_id', $userId)->first();

        $foods = Food::with('nutritionLibrary')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->get()
            ->groupBy('meal_type');

        $activities = Activity::where('user_id', $userId)
            ->where('activity_date', $date)
            ->get();

        return new DashboardResource([
            'summary' => $summary,
            'goal' => $goal,
            'foods' => $foods,
            'activities' => $activities,
        ]);
    }
}
