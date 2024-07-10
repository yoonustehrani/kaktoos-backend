<?php

namespace App\Http\Controllers;

use App\Models\AirBooking;
use Illuminate\Http\Request;

class AirBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $request->user();
    }

    /**
     * Display the specified resource.
     */
    public function show(AirBooking $airBooking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AirBooking $airBooking)
    {
        //
    }
}
