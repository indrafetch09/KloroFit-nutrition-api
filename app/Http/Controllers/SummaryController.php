<?php

namespace App\Http\Controllers;

use App\Models\Summary;
use SummaryActivityService;
use App\Services\SummaryService;
use App\Services\SummaryFoodService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SummaryResource;
use Illuminate\Support\Facades\Request;

class SummaryController extends Controller
{

    public function __construct(
        SummaryFoodService $foodService,
        SummaryActivityService $activityService
    ) {
        $this->foodService = $foodService;
        $this->activityService = $activityService;
    }
    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $userId = auth()->id(); // dari token
        $date = $request->input('date');

        // Jalankan kalkulasi summary makanan & aktivitas
        $this->foodService->generate($userId, $date);
        $this->activityService->generate($userId, $date);

        return response()->json([
            'message' => 'Summary generated successfully.',
            'date' => $date
        ]);
    }
}
