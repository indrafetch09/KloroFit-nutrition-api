<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityController
{   
    // Add activity                 
    public function store(ActivityRequest $request) 
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id(); // Set user_id from authenticated user

        $activity = Activity::create([$data]);

        return response()->json([
            'success' => true,
            'message' => 'Activity created successfully',
            'data' => $activity
        ], 201);
    }
    
    // Update activity
    public function update($id, ActivityRequest $request) 
    {
        
    }              
    
    // Delete activity
    public function destroy($id) 
    {

    }
}
