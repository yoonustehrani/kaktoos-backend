<?php

namespace App\Parto\Domains\Flight\Enums;

enum FlightLocationType: string {
    case None = 'none';
    case City = 'city';
    case Airport = 'airport';
    case Country = 'country';
}