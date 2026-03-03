<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    // Show login form
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    // Show register form
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

// Logout route
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/')->with('status', 'You have been logged out successfully.');
})->name('logout');

// Profile route (protected - only for logged in users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('auth.profile');
    })->name('profile');
});
