<?php

use App\Parto\Domains\Flight\FlightBook\AirTraveller;
use App\Parto\Parto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return __('validation.passenger_type_check', [
        'age' => '12',
        'type' => __('adult'),
        'other_type' => __('child')
    ]);
    return view('welcome');
});


Route::get('/parto/revalidate', function(Request $request) {
    $request->validate([
        'ref' => 'required'
    ]);
    return Parto::revalidate($request->query('ref'));
});