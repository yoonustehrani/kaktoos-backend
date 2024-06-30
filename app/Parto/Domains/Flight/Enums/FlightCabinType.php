<?php

namespace App\Parto\Domains\Flight\Enums;

enum FlightCabinType: string {
    case Y = 'economy';
    case S = 'premium-economy';
    case C = 'business';
    case J = 'premium-business';
    case F = 'first';
    case P = 'premium-first';
    case Default = 'default';
}