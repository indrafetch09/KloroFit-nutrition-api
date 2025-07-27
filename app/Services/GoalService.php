<?php

namespace App\Services;

use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class GoalService
{
    public function createOrUpdateGoal(int $userId, array $data): Goal
    {
        $goal = Goal::updateOrCreate(
            ['user_id' => $userId, 'date' => $data['date']],
            $data
        );
        return $goal;
    }

    public function getGoalByDate(int $userId, string $date): ?Goal
    {
        return Goal::where('user_id', $userId)->whereDate('date', $date)->first();
    }

    public function getGoalsForUser(int $userId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = Goal::where('user_id', $userId);

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function deleteGoal(Goal $goal): ?bool
    {
        return $goal->delete();
    }
}
