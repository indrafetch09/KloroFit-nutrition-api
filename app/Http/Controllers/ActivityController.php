<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Services\SummaryService;
use App\Http\Resources\ActivityResource;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function index()
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
            'data' => ActivityResource::collection($activities),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'activity_date' => 'required|date|date_format:Y-m-d',
            'type' => ActivityType::rules(),
            'duration_minutes' => 'required|numeric|min:1|max:1440',
            'distance' => 'nullable|numeric|min:0',
            'calories_burned' => 'required|numeric|min:0',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $user->id;

        $food = Activity::create($data);

        // Update summary harian
        SummaryService::generate($user->id, $data['activity_date']);

        return response()->json([
            'success' => true,
            'message' => 'Activity added and summary updated.',
            'data' => $food,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $activity = Activity::where('user_id', Auth::id())->findOrFail($id);
        $oldDate = $activity->activity_date;

        $activity->update($request->validated());
        $newDate = $request->activity_date ?? $oldDate;

        SummaryService::generate(Auth::id(), $oldDate);

        if ($oldDate !== $newDate) {
            SummaryService::generate(Auth::id(), $newDate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Activity updated & summary generated',
            'data' => $activity,
        ]);
    }

    public function destroy($id)
    {
        $activity = Activity::where('user_id', Auth::id())->findOrFail($id);
        $date = $activity->activity_date;
        $activity->delete();

        SummaryService::generate(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted & summary updated',
        ]);
    }
}
