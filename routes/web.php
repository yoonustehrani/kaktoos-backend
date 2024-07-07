<?php

use App\Parto\Parto;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/parto', function() {
    // return Parto::revalidate("61616635336634663936366334646636613835633634346335363366656337332635322635363737353535");
});