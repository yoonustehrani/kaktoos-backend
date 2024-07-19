<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');