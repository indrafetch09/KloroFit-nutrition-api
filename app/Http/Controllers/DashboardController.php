<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Goal;
use App\Models\SummaryFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DashboardResource;
use App\Models\SummaryActivity;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::id();
        $date = $request->query('date', now()->toDateString());

        $foodsummary = SummaryFood::where('user_id', $user)->where('date', $date)->first();
        $goal = Goal::where('user_id', $user)->first();

        $foods = Food::with('nutritionLibrary')
            ->where('user_id', $user)
            ->where('date', $date)
            ->get()
            ->groupBy('meal_type');

        $activities = SummaryActivity::where('user_id', $user)
            ->where('activity_date', $date)
            ->get();

        return new DashboardResource([
            'summary' => $foodsummary,
            'goal' => $goal,
            'foods' => $foods,
            'activities' => $activities,
        ]);
    }
}
