<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\GoalRequest;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    // GET /goals → ambil goal user
    public function index()
    {
        $goal = Goal::where('user_id', Auth::id())->first();

        if (!$goal) {
            return response()->json([
                'success' => false,
                'message' => 'Goal empty',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Goal fetched',
            'data' => $goal
        ]);
    }

    // POST /goals → set goal pertama kali
    public function store(GoalRequest $request): JsonResponse
    {
        $user = $request->user(); // user yang login via sanctum

        // Cek duplikat goal di tanggal yang sama
        $existing = Goal::where('user_id', $user->id)
            ->where('date', $request->date)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Goal untuk tanggal ini sudah ada.'
            ], 409);
        }

        $goal = Goal::create([
            'user_id'  => $user->id,
            'date'     => $request->date,
            'calories' => $request->calories,
            'carbs'    => $request->carbs,
            'protein'  => $request->protein,
            'fat'      => $request->fat,
        ]);

        return response()->json([
            'message' => 'Goal berhasil dibuat.',
            'data'    => $goal
        ], 201);
    }


    public function update(GoalRequest $request, $id): JsonResponse
    {
        $user = $request->user();
        $goal = Goal::where('id', $id)->where('user_id', $user->id)->first();

        if (!$goal) {
            return response()->json(['message' => 'Goal tidak ditemukan.'], 404);
        }

        $goal->update($request->validated());

        return response()->json([
            'message' => 'Goal berhasil diperbarui.',
            'data' => $goal
        ]);
    }


    //     // PUT /goals → update goal jika sudah ada
    //     public function update(Request $request)
    //     {
    //         $request->validate([
    //             'calories' => 'required|integer|min:0',
    //             'protein'  => 'required|integer|min:0',
    //             'fat'      => 'required|integer|min:0',
    //             'carbs'    => 'required|integer|min:0',
    //         ]);

    //         $goal = Goal::where('user_id', Auth::id())->first();

    //         if (!$goal) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Goal not found. Set it first.',
    //             ], 404);
    //         }

    //         $goal->update($request->only(['calories', 'protein', 'fat', 'carbs']));

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Goal anda berhasil diperbarui',
    //             'data' => $goal
    //         ]);
    //     }
}
