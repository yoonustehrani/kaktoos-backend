<?php

namespace App\Parto\Domains\Flight\Enums\AirBook;

use App\Attributes\Description;
use App\Traits\EnumAttributeCatcher;

enum AirQueueStatus: int
{
    use EnumAttributeCatcher;
    
    #[Description('رزرو شده')]
    case Booked = 10;

    #[Description('معلق')]
    case Pending = 11;

    #[Description('در لیست انتظار')]
    case Waitlist = 12;

    #[Description('در حال صدور')]
    case TicketinProcess = 20;

    #[Description('صادر شده')]
    case Ticketed  = 21;

    #[Description('تغییر یافته توسط مسافر')]
    case TicketedChanged = 22;

    #[Description('تغییر زمان پرواز توسط ایرلاین')]
    case TicketedScheduleChange = 23;

    #[Description('باطل شده')]
    case TicketedCancelled = 24;

    #[Description('باطل شده')]
    case TicketedVoid = 25;

    #[Description('باطل شده')]
    case Cancelled = 30;

    #[Description('ناموفق')]
    case Exception = 40;

    #[Description('ناموفق')]
    case Gateway = 41;

    #[Description('ناموفق')]
    case Duplicate = 42;

    public function getDescription()
    {
        return $this->getAttributeValue(Description::class);
    }
}