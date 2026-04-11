<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Services\ActivityService;
use App\Services\SummaryActivity;


class ActivityController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }


    public function index()
    {
        $activities = UserActivity::where('user_id', Auth::id())
            ->orderBy('activity_date', 'desc')
            ->get();

        if ($activities->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum menambahkan aktivitas hari ini.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil ditambahkan.',
            'data' => ActivityResource::collection($activities),
        ]);
    }

    public function store(ActivityRequest $request)
    {

        $validatedData = $request->validated();
        $validatedData['user_id'] = $request->user()->id;

        // Panggil service untuk membuat record baru
        $activity = UserActivity::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil ditambahkan.',
            'data' => $activity
        ], 201);
    }


    public function update($id, Request $request)
    {
        $activity = UserActivity::where('user_id', Auth::id())->findOrFail($id);
        $oldDate = $activity->activity_date;

        $activity->update($request->validated());
        $newDate = $request->activity_date ?? $oldDate;

        SummaryActivityService::recalculateSummary(Auth::id(), $oldDate);

        if ($oldDate !== $newDate) {
            SummaryActivityService::recalculateSummary(Auth::id(), $newDate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Activity updated',
            'data' => $activity,
        ]);
    }

    public function destroy($id)
    {
        $activity = UserActivity::where('user_id', Auth::id())->findOrFail($id);
        $date = $activity->activity_date;
        $activity->delete();

        SummaryActivityService::recalculateSummary(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted & summary updated',
        ]);
    }
}
