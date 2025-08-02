<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use App\Services\SummaryActivityService;
use App\Http\Resources\ActivityResource;
use App\Enums\ActivityType;
use App\Http\Requests\ActivityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Global_;
use SummaryActivityService as GlobalSummaryActivityService;

class ActivityController extends Controller
{
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
            'data' => ActivityResource::create() // Atau gunakan ActivityResource
        ], 201);
    }

    public function update($id, Request $request)
    {
        $activity = UserActivity::where('user_id', Auth::id())->findOrFail($id);
        $oldDate = $activity->activity_date;

        $activity->update($request->validated());
        $newDate = $request->activity_date ?? $oldDate;

        GlobalSummaryActivityService::recalculateSummary(Auth::id(), $oldDate);

        if ($oldDate !== $newDate) {
            GlobalSummaryActivityService::recalculateSummary(Auth::id(), $newDate);
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

        GlobalSummaryActivityService::recalculateSummary(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted & summary updated',
        ]);
    }
}
