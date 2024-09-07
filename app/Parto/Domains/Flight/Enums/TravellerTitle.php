<?php

namespace App\Parto\Domains\Flight\Enums;

enum TravellerTitle: int
{
    case Mr  = 0;
    case Mrs = 1;
    case Ms  = 2;
    case Miss = 3;
    case Mstr = 4;
}