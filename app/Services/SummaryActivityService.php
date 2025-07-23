<?php

use App\Models\SummaryActivity;
use App\Models\UserActivity;

class SummaryActivityService
{
    public static function recalculateSummary(int $userId, string $date): void
    {
        $activities = UserActivity::where('user_id', $userId)
            ->where('date', $date)
            ->get();

        $calories = $activities->sum('calories_burned');
        $duration = $activities->sum('duration_minutes');

        SummaryActivity::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'calories_burned' => $calories,
                'duration_minutes' => $duration,
                'activity_count' => $activities->count(),
            ]
        );
    }
}
