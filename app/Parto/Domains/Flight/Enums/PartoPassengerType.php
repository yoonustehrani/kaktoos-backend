<?php

namespace App\Parto\Domains\Flight\Enums;

enum PartoPassengerType: int {
    // case SeniorAdt = 0;
    case Adt = 1;
    case Chd = 2;
    case Inf = 3;
}