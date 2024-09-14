<?php

use App\Events\OrderPaid;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Models\AirBooking;
use App\Models\Order;
use App\Parto\Enums\HotelQueueStatus;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


Route::get('/ticket', function() {
    $airBooking = AirBooking::latest()->first();
    $airBooking->load(['passengers.tickets', 'flights' => function($query) {
        $query->with(['arrival_airport.country', 'departure_airport.country', 'marketing_airline', 'operating_airline']);
    }]);
    $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
    return view('pdfs.ticket2')
        ->with('passengers', $airBooking->passengers)
        ->with('flights', $airBooking->flights);
});

Route::get('/ticket/data', function() {
    $airBooking = AirBooking::latest()->first();
    $airBooking->load(['passengers.tickets', 'flights' => function($query) {
        $query->with(['arrival_airport.country', 'departure_airport.country', 'marketing_airline', 'operating_airline']);
    }]);
    $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
    return $airBooking;
});

Route::middleware('auth:sanctum')->group(function() {
    Route::get('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    Route::get('air/bookings/{airBooking}/ticket', [TicketController::class, 'index'])->name('bookings.air.tickets.index');
});

Route::view('/success', 'transaction.success');
Route::view('/fail', 'transaction.fail');

Route::view('/', 'welcome');