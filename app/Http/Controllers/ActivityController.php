<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Services\SummaryService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;

class ActivityController extends Controller
{
    public function show()
    {
        $activities = Activity::where('user_id', Auth::id())
            ->orderBy('activity_date', 'desc')
            ->get();

        if ($activities->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'You have not added any activities yet',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Activities fetched successfully',
            'data' => new ActivityResource($activities),
        ]);
    }

    public function store(ActivityRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $activity = Activity::create($data);

        SummaryService::updateUserSummary(Auth::id(), $data['activity_date']);

        return response()->json([
            'success' => true,
            'message' => 'Activity added & summary updated',
            'data' => $activity,
        ]);
    }

    public function update($id, ActivityRequest $request)
    {
        $activity = Activity::where('user_id', Auth::id())->findOrFail($id);
        $oldDate = $activity->activity_date;

        $activity->update($request->validated());
        $newDate = $request->activity_date ?? $oldDate;

        SummaryService::updateUserSummary(Auth::id(), $oldDate);

        if ($oldDate !== $newDate) {
            SummaryService::updateUserSummary(Auth::id(), $newDate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Activity updated & summary updateUserSummaryd',
            'data' => $activity,
        ]);
    }

    public function destroy($id)
    {
        $activity = Activity::where('user_id', Auth::id())->findOrFail($id);
        $date = $activity->activity_date;
        $activity->delete();

        SummaryService::updateUserSummary(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted & summary updated',
        ]);
    }
}
