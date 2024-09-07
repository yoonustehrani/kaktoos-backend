<?php

namespace App\Parto\Domains;

use App\Parto\Domains\Flight\FlightBook\FlightBook;
use App\Parto\Domains\Flight\FlightSearch\FlightSearch;

class FlightService
{
    public function __construct()
    {
        
    }

    public function flightSearch()
    {
        return new FlightSearch;
    }

    public function flightBook()
    {
        return new FlightBook;
    }
}