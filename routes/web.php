<?php

use App\Events\OrderPaid;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Jobs\OrderTransactionPaid;
use App\Models\AirBooking;
use App\Models\Order;
use App\Models\Transaction;
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
    abort_if(app()->isProduction(), 403);
    $airBooking = AirBooking::latest()->first();
    $airBooking->load('origin_airport', 'destination_airport');
    $airBooking->load(['passengers.tickets', 'flights' => function($query) {
        $query->with(['arrival_airport.country', 'departure_airport.country', 'marketing_airline', 'operating_airline']);
    }]);
    // return $airBooking;
    $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
    $view = view('pdfs.ticket-fa')
        ->with('booking', $airBooking)
        ->with('passengers', $airBooking->passengers)
        ->with('flights', $airBooking->flights);
    return $view;
    // return $view->render();
    // $response = Http::post('http://pdfrenderer:8082/render', [
    //     'html' => $view->render(), // Render a Blade view
    // ]);
    // $pdf = $response->body();
    // return response($pdf, 200, [
    //     'Content-Type' => 'application/pdf'
    // ]);
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
    // Route::get('hotel/bookings/{hotelBooking}/voucher');
});

Route::get('/trx', function() {
    $trx = Transaction::latest()->first();
    $trx->order->user->increaseCredit($trx->amount);
    OrderTransactionPaid::dispatch($trx);
});

Route::view('/', 'welcome');