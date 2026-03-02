<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Rating;

class RatingController extends Controller
{
    public function index($registration)
    {
        // Find the car by registration number
        $car = Car::where('registration_number', $registration)->first();

        if (!$car) {
            return response()->json(['error' => 'Car not found'], 404);
        }

        // Return all ratings for this car
        return response()->json([
            'ratings' => $car->ratings
        ]);
    }

    public function store(Request $request, $registration)
    {
        // Find the car by registration number
        $car = Car::where('registration_number', $registration)->first();

        if (!$car) {
            return response()->json(['error' => 'Car not found'], 404);
        }

        // Validate the request data
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'location' => 'nullable|string'
        ]);

        // Create the rating (for now, we'll use a dummy user ID)
        $rating = Rating::create([
            'car_id' => $car->id,
            'user_id' => 1, // For now, we'll use the test user we created
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'location' => $validated['location']
        ]);

        return response()->json([
            'message' => 'Rating created successfully',
            'rating' => $rating
        ], 201);
    }
}
