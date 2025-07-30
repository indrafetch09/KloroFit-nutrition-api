<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NutritionLibrary;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NutritionLibraryResource;
use Illuminate\Http\Response;

class NutritionLibraryController extends Controller
{
    public function __construct()
    {
        // Middleware auth untuk semua method
        $user = Auth::user();
    }

    /**
     * Search for food by name or display all.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByName(Request $request)
    {
        $query = $request->query('q');

        if ($query) {
            $foods = NutritionLibrary::where('name', 'LIKE', "%{$query}%")->paginate(10);
            $message = $foods->isEmpty() ? 'Ups.. data makanan kosong.' : 'Data makanan berhasil ditemukan';
        } else {
            $foods = NutritionLibrary::paginate(10);
            $message = $foods->isEmpty() ? 'Ups.. data makanan kosong.' : 'Ini adalah semua data makanan yang telah kamu masukkan .';
        }

        return NutritionLibraryResource::collection($foods)->additional([
            'message' => $message,
            'success' => true,
        ], 200);
    }

    /**
     * Display food details by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = NutritionLibrary::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Data makanan kosong.',
                'success' => false,
            ], 404);
        }

        return (new NutritionLibraryResource($item))->additional([
            'message' => 'Data makanan berhasil ditemukan.',
            'success' => true,
        ], 200);
    }

    // // ✅ Simpan data makanan baru
    // public function store(Request $request)
    // {
    //     $user = Auth::user();

    //     // 🔒 Validasi hanya admin yang bisa menambah makanan
    //     if (!$user || !$user->is_admin) {
    //         return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
    //     }

    //     $data = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'calories' => 'required|numeric|min:0',
    //         'fat' => 'required|numeric|min:0',
    //         'protein' => 'required|numeric|min:0',
    //         'carbs' => 'required|numeric|min:0',
    //         'is_verified' => 'boolean'
    //     ]);

    //     $food = NutritionLibrary::create($data);
    //     return new NutritionLibraryResource($food);
    // }

    // // ✅ Update data makanan
    // public function update(Request $request, $id)
    // {
    //     $user = Auth::user();

    //     // 🔒 Validasi hanya admin yang bisa update makanan
    //     if (!$user || !$user->is_admin) {
    //         return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
    //     }

    //     $food = NutritionLibrary::findOrFail($id);

    //     $data = $request->validate([
    //         'name' => 'string|max:255',
    //         'calories' => 'numeric|min:0',
    //         'fat' => 'numeric|min:0',
    //         'protein' => 'numeric|min:0',
    //         'carbs' => 'numeric|min:0',
    //         'is_verified' => 'boolean'
    //     ]);

    //     $food->update($data);
    //     return new NutritionLibraryResource($food);
    // }

    // // ✅ Hapus data makanan
    // public function destroy($id)
    // {
    //     $user = Auth::user();

    //     // 🔒 Validasi hanya admin yang bisa hapus makanan
    //     if (!$user || !$user->is_admin) {
    //         return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
    //     }

    //     $food = NutritionLibrary::findOrFail($id);
    //     $food->delete();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Nutrition library entry deleted.'
    //     ]);
    // }
}
