<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NutritionLibraryController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\SummaryFoodController;
use App\Models\NutritionLibrary;
use Illuminate\Routing\RouteGroup;

Route::prefix('v1')->group(function () {
    // Auth routes
    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'message' => 'Welcome to Klorofit API',
            'data' => null
        ]);
    });

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

        // Nutrition Library search
        Route::prefix('nutrition-libraries')->group(function () {
            Route::get('/search', [NutritionLibraryController::class, 'searchByName']);
        });


        // Set Goals
        Route::prefix('goals')->group(function () {
            Route::get('/', [GoalController::class, 'index']); // Mengambil semua goals
            Route::get('/{date}', [GoalController::class, 'show']); // Mengambil goal spesifik by date
            Route::post('/set', [GoalController::class, 'storeOrUpdate']); // Untuk membuat/memperbarui goal harian
            Route::delete('/{date}', [GoalController::class, 'destroy']); // Menghapus goal by date
        });

        // Food tracking
        Route::prefix('foods')->group(function () {
            Route::get('/{date}', [FoodController::class, 'index']);
            Route::post('/set', [FoodController::class, 'store']);
            Route::put('/{id}', [FoodController::class, 'update']);
            Route::delete('/{id}', [FoodController::class, 'destroy']);
        });

        // Activity tracking 
        Route::prefix('activities')->group(function () {
            Route::get('/', [ActivityController::class, 'index']);
            Route::post('/set', [ActivityController::class, 'store']);
            Route::put('/{id}', [ActivityController::class, 'update']);
            Route::delete('/{id}', [ActivityController::class, 'destroy']);
        });

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'show']);

        // Profile
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);

        // password
        Route::put('/password', [UserController::class, 'updatePassword']);

        // settings
        Route::get('/settings', [UserController::class, 'getSettings']);
        Route::put('/settings', [UserController::class, 'updateSettings']);
    });
});

// Fallback untuk route tidak ditemukan
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
