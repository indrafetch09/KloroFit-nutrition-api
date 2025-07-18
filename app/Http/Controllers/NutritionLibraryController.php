<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NutritionLibrary;
use App\Http\Resources\NutritionLibraryResource;

class NutritionLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q');

        if ($query) {
            $foods = NutritionLibrary::where('name', 'LIKE', "%{$query}%")->paginate(10);
        } else {
            $foods = NutritionLibrary::paginate(10); // default: tampilkan semua
        }

        return NutritionLibraryResource::collection($foods);
    }

    // ✅ Tampilkan detail makanan berdasarkan ID
    public function show($id)
    {
        $item = NutritionLibrary::findOrFail($id);
        return new NutritionLibraryResource($item);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'calories' => 'required|numeric',
            'fat' => 'required|numeric',
            'protein' => 'required|numeric',
            'carbs' => 'required|numeric',
            'is_verified' => 'boolean'
        ]);

        $food = NutritionLibrary::create($data);
        return new NutritionLibraryResource($food);
    }

    public function update(Request $request, $id)
    {
        $food = NutritionLibrary::findOrFail($id);

        $data = $request->validate([
            'name' => 'string',
            'calories' => 'numeric',
            'fat' => 'numeric',
            'protein' => 'numeric',
            'carbs' => 'numeric',
            'is_verified' => 'boolean'
        ]);

        $food->update($data);
        return new NutritionLibraryResource($food);
    }

    // ✅ Hapus data makanan
    public function destroy($id)
    {
        $food = NutritionLibrary::findOrFail($id);
        $food->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nutrition library entry deleted.'
        ]);
    }
}
