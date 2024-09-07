<?php

namespace App\Parto\Domains\Flight\Enums;

enum TravellerSeatPreference: string
{
    case Any = 'any';
    case A = 'aisle';
    case W = 'window';
}