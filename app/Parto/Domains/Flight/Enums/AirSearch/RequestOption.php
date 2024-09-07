<?php

namespace App\Parto\Domains\Flight\Enums\AirSearch;

enum RequestOption: int
{
    case Fifty = 0;
    case Hundred = 1;
    case TwoHundred = 2;
    case All = 3;
}