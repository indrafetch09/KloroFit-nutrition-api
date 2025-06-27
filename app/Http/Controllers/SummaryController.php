<?php

namespace App\Http\Controllers;

use App\Http\Resources\SummaryResource;
use App\Models\Summary;
use App\Services\SummaryService;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    public function show($date)
    {
        $userId = Auth::id();

        // Pastikan summary up-to-date
        SummaryService::updateUserSummary($userId, $date);

        $summary = Summary::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $summary ? new SummaryResource($summary) : null,
        ]);
    }
}
