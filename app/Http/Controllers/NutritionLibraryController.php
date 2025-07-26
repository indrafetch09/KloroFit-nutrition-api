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

    // ✅ Search makanan berdasarkan nama atau tampilkan semua
    public function searchByName(Request $request)
    {
        $query = $request->query('q');

        if ($query) {
            $foods = NutritionLibrary::where('name', 'LIKE', "%{$query}%")->paginate(10);
        } else {
            $foods = NutritionLibrary::paginate(10);
        }

        return NutritionLibraryResource::collection($foods);
    }

    // ✅ Tampilkan detail makanan berdasarkan ID
    public function show($id)
    {
        $item = NutritionLibrary::findOrFail($id);
        return new NutritionLibraryResource($item);
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
