<?php

use App\Http\Controllers\AirBookingController;
use App\Http\Controllers\FlightPriceController;
use App\Http\Controllers\FlightRulesController;
use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\InternationalAirportController;
use App\Http\Controllers\NationalAirportController;
use App\Http\Controllers\UserAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('airports')->group(function() {
    Route::get('/national', NationalAirportController::class . '@index');
    Route::get('/international', InternationalAirportController::class . '@index');
});

Route::prefix('flights')->group(function() {
    Route::post('search/{method}', [FlightSearchController::class, 'index']);
    Route::get('prices', [FlightPriceController::class, 'show']);
    Route::post('rules/fare',[FlightRulesController::class, 'fare']);
    Route::post('rules/baggage', [FlightRulesController::class, 'baggage']);
    Route::post('reserve', [AirBookingController::class, 'store'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function() {
    Route::get('bookings/air/{airBooking}', [AirBookingController::class, 'show'])->name('bookings.air.show');
});

Route::post('/login', [UserAuthController::class, 'login']);

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'phone_number' => 'required',
        'password' => 'required',
    ]);
 
    $user = User::where('phone_number', $request->phone_number)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'phone_number' => ['The provided credentials are incorrect.'],
        ]);
    }
 
    return $user->createToken('thunderbelt')->plainTextToken;
});