<?php

namespace App\Parto\Domains\Flight\Enums\AirSearch;

enum PricingSourceType: int
{
    case All = 0;
    case Private = 1;
    case Publish = 2;
    case WebFare = 3;
}