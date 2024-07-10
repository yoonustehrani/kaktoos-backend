<?php

namespace App\Parto\Domains\Flight\Enums;

enum TravellerPassengerType: string 
{
    case SeniorAdt = 'senior-adult';
    case Adt = 'adult';
    case Chd = 'child';
    case Inf = 'infant';
}