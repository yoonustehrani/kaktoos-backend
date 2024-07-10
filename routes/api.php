<?php

use App\Http\Controllers\AirBookingController;
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
    Route::post('search/{method}', [FlightApiController::class, 'search']);
    Route::get('prices', [FlightPriceController::class, 'show']);
    Route::post('rules/fare',[FlightApiController::class, 'getFareRules']);
    Route::post('rules/baggage', [FlightApiController::class, 'getBaggageRules']);
    Route::post('reserve', [AirBookingController::class, 'store'])->middleware('auth:sanctum');
});

Route::post('/login', [UserAuthController::class, 'login']);