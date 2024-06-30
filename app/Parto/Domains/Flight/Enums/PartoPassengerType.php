<?php

namespace App\Parto\Domains\Flight\Enums;

enum PartoPassengerType: int {
    case Adult = 1;
    case Child = 2;
    case Infant = 3;
}