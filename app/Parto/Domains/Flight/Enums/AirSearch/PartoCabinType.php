<?php

namespace App\Parto\Domains\Flight\Enums\AirSearch;

enum PartoCabinType: int {
    case Y = 1;
    case S = 2;
    case C = 3;
    case J = 4;
    case F = 5;
    case P = 6;
    case Default = 100;
}