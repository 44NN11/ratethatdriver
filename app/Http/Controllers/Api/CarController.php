<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    public function show($registration)
    {
        // Find car by registration number, or create it if it doesn't exist
        $car = Car::firstOrCreate([
            'registration_number' => $registration
        ]);

        // Return car with its ratings
        return response()->json([
            'car' => $car,
            'ratings' => $car->ratings
        ]);
    }
}
