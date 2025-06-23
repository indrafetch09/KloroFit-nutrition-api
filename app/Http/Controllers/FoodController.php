<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FoodController extends Controller
{
    // List foods
    public function index()
    {
        validate(request(), [
            'date' => 'date|nullable',
            'meal_type' => 'in:breakfast,lunch,dinner,snack|nullable',
        ]);

        

        $data = [
            'message' => 'List of foods',
            'status' => 'success',
            // Add more data as needed
        ];

        return response()->json($data);
    }       
    
    // Add foods
    public function store(foodsRequest $request)
    {

    } 

    // Update foods
    public function update($id, FoodsRequest $request)
    {

    } 

    // Delete foods
    public function destroy($id)
    {

    }              
};
