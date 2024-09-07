<?php

namespace App\Parto\Domains\Flight\Enums\AirSearch;

enum AirTripType: int {
    case OneWay  = 1;
    case Return  = 2;
    case Circle  = 3;
    case OpenJaw = 4;
}