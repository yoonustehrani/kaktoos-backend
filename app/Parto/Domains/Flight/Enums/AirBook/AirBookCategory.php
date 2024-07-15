<?php

namespace App\Parto\Domains\Flight\Enums\AirBook;

enum AirBookCategory: int 
{
    case Booked = 10;
    // case TicketinProcess = 20;
    // case Ticketed = 30;
    case Cancelled = 30;
    // case Exception = 50;
}