<?php

namespace App\Parto\Domains\Flight\Enums;

enum PartoFareType: int {
    case Default = 1;
    case Publish = 2;
    case Private = 3;
    case WebFare = 4;
    case NegoCat35 = 5;
    case NegoCorporate = 6;
    case AmadeusNegoCorporate = 7;
    case PrivateCat15 = 8;
    case AmadeusNego = 9;
    case NetFare = 10;
    // case Private = 3;
}