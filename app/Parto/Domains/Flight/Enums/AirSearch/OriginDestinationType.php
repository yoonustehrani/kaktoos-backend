<?php

namespace App\Parto\Domains\Flight\Enums\AirSearch;

enum OriginDestinationType: int
{
    case None    = 0;
    case City    = 1;
    case Airport = 2;
    case RailStation = 3;
    case Hotel   = 4;
    case Country = 5;
}