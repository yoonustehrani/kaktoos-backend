<?php

namespace App\Http\Controllers;

use App\Models\AirBooking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function verify(Request $request, string $gateway)
    {
        return AirBooking::find($request->input('clientReferenceNumber'));
    }
}