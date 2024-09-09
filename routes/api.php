<?php

use App\Http\Controllers\AirBookingController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FlightPriceController;
use App\Http\Controllers\FlightRulesController;
use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\InternationalAirportController;
use App\Http\Controllers\NationalAirportController;
use App\Http\Controllers\Parto\HotelImageController;
use App\Http\Controllers\UserAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::prefix('airports')->group(function() {
    Route::get('/national', NationalAirportController::class . '@index');
    Route::get('/international', InternationalAirportController::class . '@index');
});

Route::prefix('flights')->group(function() {
    Route::post('search/{method}', FlightSearchController::class);
    Route::get('prices', [FlightPriceController::class, 'show']);
    Route::post('rules/fare',[FlightRulesController::class, 'fare']);
    Route::post('rules/baggage', [FlightRulesController::class, 'baggage']);
    Route::post('reserve', [AirBookingController::class, 'store'])->middleware('auth:sanctum');
});

Route::prefix('/user')->name('user.')->middleware('auth:sanctum')->group(function() {
    Route::get('/', fn(Request $request) => $request->user());
    Route::prefix('/bookings')->name('bookings.')->group(function() {
        Route::get('/air/{airBooking}', [AirBookingController::class, 'show'])->name('air.show');
        Route::get('/air/{airBooking}/details', [AirBookingController::class, 'showDetailed'])->name('air.show.detailes');
        Route::get('/hotel/{hotelBooking}', fn() => ['okay' => true])->name('hotel.show');
    });
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


Route::get('cities/search', [CityController::class, 'search']);

Route::prefix('hotels')->group(function() {
    Route::get('/search', [HotelController::class, 'search']);
    Route::get('/images', [HotelImageController::class, 'index']);
    Route::post('/city/{cityId}/offers', [HotelController::class, 'showCity']);
    Route::prefix('/offers')->name('offers.')->group(function() {
        Route::get('/{ref}', [HotelController::class, 'checkHotelOffer'])->name('show'); // check offer
        Route::post('/{ref}/order', [HotelBookingController::class, 'store'])->middleware('auth:sanctum')->name('order'); // order offer
    });
    Route::get('/{hotelId}/images', [HotelImageController::class, 'show']);
    Route::post('/{hotelId}/offers', [HotelController::class, 'hotelOffers']);
    Route::get('/{hotelId}', [HotelController::class, 'show']);
});