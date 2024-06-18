<?php

use App\Parto\Domains\Flight\Enums\FlightCabinType;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
