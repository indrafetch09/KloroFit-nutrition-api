<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NutritionLibraryController;


Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Verifikasi email (biasanya otomatis via Laravel built-in)
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed'])->name('verification.verify');
});

// // HOME / DASHBOARD
// Route::middleware(['auth:sanctum', 'verified'])->prefix('home')->group(function () {
//     Route::get('/summary', [HomeController::class, 'summary']);
//     Route::get('/graph', [HomeController::class, 'graph']);
// });

// // NUTRITION LIBRARY (Referensi makanan)
// Route::middleware('auth:sanctum')->prefix('nutrition-library')->group(function () {
//     Route::get('/', [NutritionLibraryController::class, 'index']);
//     Route::get('/{id}', [NutritionLibraryController::class, 'show']);
// });

// // FOOD TRACKING
// Route::middleware(['auth:sanctum', 'verified'])->prefix('foods')->group(function () {
//     Route::get('/', [FoodController::class, 'index']);
//     Route::post('/', [FoodController::class, 'store']);
//     Route::put('/{id}', [FoodController::class, 'update']);
//     Route::delete('/{id}', [FoodController::class, 'destroy']);
// });

// // ACTIVITY TRACKING
// Route::middleware(['auth:sanctum', 'verified'])->prefix('activities')->group(function () {
//     Route::get('/', [ActivityController::class, 'index']);
//     Route::post('/', [ActivityController::class, 'store']);
//     Route::put('/{id}', [ActivityController::class, 'update']);
//     Route::delete('/{id}', [ActivityController::class, 'destroy']);
// });

// // PROFILE & GOAL
// Route::middleware(['auth:sanctum', 'verified'])->prefix('profile')->group(function () {
//     Route::get('/', [ProfileController::class, 'show']);
//     Route::put('/', [ProfileController::class, 'update']);
//     Route::put('/goal', [ProfileController::class, 'updateGoal']);
//     Route::put('/password', [ProfileController::class, 'changePassword']);
// });

// Fallback untuk route tidak ditemukan
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
