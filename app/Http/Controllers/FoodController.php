<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\FoodService;
use App\Http\Requests\FoodRequest;
use App\Http\Resources\FoodResource;
use App\Http\Requests\StoreFoodRequest;

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
        $userId = $request->user()->id;
        $date = $request->query('date'); // Ambil parameter 'date' dari URL query

        try {
            if ($date) {
                // Jika parameter 'date' ada, panggil method service dengan filter tanggal
                $foods = $this->foodService->getAllFoodsForUserByDate($userId, $date);
            } else {
                // Jika tidak ada parameter 'date', panggil method service tanpa filter
                $foods = $this->foodService->getAllFoodsForUser($userId);
            }

            if ($foods->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data makanan yang ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data makanan berhasil diambil.',
                'data' => FoodResource::collection($foods)
            ], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan umum
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data makanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created food entry in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FoodRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = $request->user()->id;

        try {
            $food = $this->foodService->createFood($validatedData);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan makanan.',
                'error' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data makanan berhasil ditambahkan.',
            'data' => new FoodResource($food)
        ], 201);
    }


    public function storeMultipleFoods(StoreFoodRequest $request)
    {
        $userId = $request->user()->id;
        $foodsData = $request->validated()['foods'];

        try {
            $foods = $this->foodService->createBulkFoods($userId, $foodsData);

            return response()->json([
                'success' => true,
                'message' => 'Data makanan berhasil ditambahkan.',
                'data' => FoodResource::collection($foods)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data makanan.',
                'error' => $e->getMessage()
            ], 500);
        }
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
            // 'message' => 'Data makanan berhasil diambil.',
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
