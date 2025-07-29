<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Http\Resources\FoodResource;
use App\Services\FoodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    protected FoodService $foodService;
    public function __construct(FoodService $foodService)
    {
        $this->foodService = $foodService;
    }

    /**
     * Display a listing of the food (for the authenticated user).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): JsonResponse
    {
        $foods = $this->foodService->getAllFoodsForUser($request->user()->id);

        if ($foods->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada data makanan yang ditemukan.',
                'data' => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data makanan berhasil diambil.',
            'data' => FoodResource::collection($foods),
        ]);
    }

    /**
     * Store a newly created food in storage.
     *
     * @param  \App\Http\Requests\FoodRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FoodRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = $request->user()->id;

        // Semua data yang dibutuhkan (nutrition_library_id, meal_type, date) sudah ada di $validatedData
        // yang datang dari FoodRequest.
        try {
            $food = $this->foodService->createFood($validatedData);
        } catch (\Exception $e) { // Bad Request jika library item tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data makanan berhasil ditambahkan.',
            'data' => new FoodResource($food)
        ], 201);
    }

    /**
     * Display the specified food.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $food = $this->foodService->getFoodById($id);

        if (!$food || $food->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Data makanan tidak ditemukan atau kamu belum menambahkannya.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data makanan berhasil diambil.',
            'data' => new FoodResource($food)
        ]);
    }

    /**
     * Update the specified food in storage.
     *
     * @param  \App\Http\Requests\FoodRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FoodRequest $request, int $id): JsonResponse
    {
        $food = $this->foodService->getFoodById($id);

        if (!$food || $food->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Data makanan tidak ditemukan atau kamu belum menambahkannya.'
            ], 404);
        }

        try {
            $updatedFood = $this->foodService->updateFood($food, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data makanan berhasil diperbarui.',
            'data' => new FoodResource($updatedFood)
        ]);
    }

    /**
     * Remove the specified food from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $food = $this->foodService->getFoodById($id);

        if (!$food || $food->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Data makanan tidak ditemukan atau kamu belum menambahkannya.'
            ], 404);
        }

        try {
            $this->foodService->deleteFood($food);
        } catch (\Exception $e) { // Internal Server Error
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data makanan berhasil dihapus.'
        ], 200);
    }
}
