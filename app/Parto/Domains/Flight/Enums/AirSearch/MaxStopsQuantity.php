<?php

namespace App\Parto\Domains\Flight\Enums\AirSearch;

enum MaxStopsQuantity: int
{
    case All = 0;
    case Direct = 2;
}