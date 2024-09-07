<?php

namespace App\Parto\Domains\Flight\Enums\AirBook;

use App\Attributes\Description;
use App\Traits\EnumAttributeCatcher;

enum AirBookCategory: int 
{
    use EnumAttributeCatcher;

    #[Description('رزرو شده')]
    case Booked = 10;

    #[Description('در حال صدور')]
    case TicketinProcess = 20;

    #[Description('صادر شده')]
    case Ticketed  = 21;

    #[Description('باطل شده')]
    case Cancelled = 30;

    #[Description('خطا')]
    case Exception = 40;

    public function getDescription()
    {
        return $this->getAttributeValue(Description::class);
    }
}