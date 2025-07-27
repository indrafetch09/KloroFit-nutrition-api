<?php

namespace App\Http\Controllers;

use App\Enums\MealType;
use App\Models\UserFood;
use Illuminate\Http\Request;
use App\Http\Requests\FoodRequest;
use App\Http\Resources\FoodResource;
use App\Services\SummaryFoodService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class FoodController extends Controller
{
    public function index()
    {
        $foods = UserFood::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        if ($foods->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum menambahkan makanan pada list.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'List makanan berhasil di-fetch.',
            'data' => FoodResource::collection($foods),
        ]);
    }

    public function store(FoodRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['portion_grams'] = $data['portion_grams'] ?? 100;

        $food = UserFood::create($data);

        SummaryFoodService::recalculateSummary($user->id, $data['date']);

        return response()->json([
            'message' => 'Makanan berhasil ditambahkan.',
            'data' => new FoodResource($food),
        ], 201);
    }

    public function update(FoodRequest $request, UserFood $userFood)
    {
        $user = $request->user();

        // Pastikan hanya user pemilik data yang bisa mengupdate
        if ($user->id !== $userFood->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ups, ada yang salah..',
            ], 403);
        }

        $data = $request->validated();

        // Jika tidak ada portion_grams dikirim, jangan overwrite jadi null
        if (!array_key_exists('portion_grams', $data)) {
            unset($data['portion_grams']);
        }

        $userFood->update($data);

        // Rehitung summary harian jika ada perubahan pada date
        SummaryFoodService::recalculateSummary($user->id, $userFood->date);

        return response()->json([
            'success' => true,
            'message' => 'List makanan berhasil diperbarui.',
            'data' => new FoodResource($userFood),
        ]);
    }


    // public function update($id, Request $request)
    // {
    //     $food = UserFood::where('user_id', Auth::id())->findOrFail($id);
    //     $oldDate = $food->date;

    //     $food->update($request->validated());

    //     $newDate = $request->date ?? $oldDate;

    //     SummaryFoodService::recalculateSummary(Auth::id(), $oldDate);
    //     if ($oldDate !== $newDate) {
    //         SummaryFoodService::recalculateSummary(Auth::id(), $newDate);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Food updated & summary refreshed.',
    //         'data' => new FoodResource($food),
    //     ]);
    // }

    public function destroy($id)
    {
        $food = UserFood::where('user_id', Auth::id())->findOrFail($id);
        $date = $food->date;
        $food->delete();

        SummaryFoodService::recalculateSummary(Auth::id(), $date);

        return response()->json([
            'success' => true,
            'message' => 'Makanan berhasil dihapus.',
        ]);
    }
}
