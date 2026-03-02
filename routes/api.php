<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\RatingController;

// Car routes
Route::get('/cars/{registration}', [CarController::class, 'show']);
Route::get('/cars/{registration}/ratings', [RatingController::class, 'index']);
Route::post('/cars/{registration}/ratings', [RatingController::class, 'store']);

// User routes (for authentication)
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
