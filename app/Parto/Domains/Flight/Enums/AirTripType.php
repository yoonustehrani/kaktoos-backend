<?php

namespace App\Parto\Domains\Flight\Enums;

enum AirTripType: string {
    case OneWay  = 'one-way';
    case Return  = 'round-trip';
    case Circle  = 'multi';
    case OpenJaw = 'multi-open';
}