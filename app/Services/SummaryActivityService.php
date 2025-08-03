<?php

namespace App\Services;

use App\Models\SummaryActivity;
use App\Models\UserActivity;

class SummaryActivityService
{
    public static function recalculateSummary(int $userId, string $date): void
    {
        $getUserAcitivties = UserActivity::where('user_id', $userId)
            ->where('date', $date)
            ->get();

        $calories = $getUserAcitivties->sum('calories_burned');
        $duration = $getUserAcitivties->sum('duration_minutes');

        SummaryActivity::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'calories_burned' => $calories,
                'duration_minutes' => $duration,
                'activity_count' => $getUserAcitivties->count(),
            ]
        );
    }
}
