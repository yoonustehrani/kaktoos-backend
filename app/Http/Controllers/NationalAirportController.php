<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;

class NationalAirportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \DB::table('airports')->whereCountryCode('IR')->whereNotNull('city_name_fa')->groupBy('city_name_fa')->select('city_name_fa', 'city_name')->get();
    }
    /**
     * Display the specified resource.
     */
    public function show(Airport $airport)
    {
        //
    }
}
