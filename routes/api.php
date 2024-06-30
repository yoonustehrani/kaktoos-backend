<?php

use App\Http\Controllers\FlightApiController;
use App\Http\Controllers\FlightPriceController;
use App\Http\Controllers\InternationalAirportController;
use App\Http\Controllers\NationalAirportController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('airports')->group(function() {
    Route::get('/national', NationalAirportController::class . '@index');
    Route::get('/international', InternationalAirportController::class . '@index');
});

Route::prefix('flights')->group(function() {
    Route::post('search/one-way', [FlightApiController::class, 'searchOneWay']);
    Route::get('prices', [FlightPriceController::class, 'show']);
});

// Route::get('/login', function() {
//     // session()->put('y', 2);
//     return session('y');
// });
Route::post('/login', [UserAuthController::class, 'login']);