<?php

namespace App\Parto\Domains;

use App\Parto\Domains\Flight\FlightSearch;

class FlightService
{
    public function __construct()
    {
        
    }

    public function flightSearch()
    {
        return new FlightSearch();
    }
}