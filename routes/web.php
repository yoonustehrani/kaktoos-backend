<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    Route::get('air/bookings/{airBooking}/ticket', [TicketController::class, 'index'])->name('bookings.air.tickets.index');
    // Route::get('hotel/bookings/{hotelBooking}/voucher');
});

Route::view('/', 'welcome');