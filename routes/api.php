<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;


Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    // Route::post('/social-login', [AuthController::class, 'socialLogin']);
    // Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    
    // Protected routes
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh-token', [AuthController::class, 'refreshTokens']);
        
        // // Dashboard
        // Route::get('/dashboard', [DashboardController::class, 'index']);
        // Route::post('/goals', [DashboardController::class, 'setGoals']);
        // Route::get('/statistics', [DashboardController::class, 'statistics']);
        
        // // Food tracking
        Route::apiResource('/foods', ActivityController::class);
        
        // Activity tracking  
        Route::apiResource('/activities', ActivityController::class);
        
        // // Profile
        // Route::get('/profile', [ProfileController::class, 'show']);
        // Route::put('/profile', [ProfileController::class, 'update']);
    });
});

// Fallback untuk route tidak ditemukan
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
