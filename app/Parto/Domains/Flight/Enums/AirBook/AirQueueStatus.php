<?php

namespace App\Parto\Domains\Flight\Enums\AirBook;

use App\Attributes\DisplayFa;
use App\Traits\DescribeEnumForAPI;
use App\Traits\EnumAttributeCatcher;

enum AirQueueStatus: int
{
    use EnumAttributeCatcher, DescribeEnumForAPI;
    
    #[DisplayFa('رزرو شده')]
    case Booked = 10;

    #[DisplayFa('معلق')]
    case Pending = 11;

    #[DisplayFa('در لیست انتظار')]
    case Waitlist = 12;

    #[DisplayFa('در حال صدور')]
    case TicketinProcess = 20;

    #[DisplayFa('صادر شده')]
    case Ticketed  = 21;

    #[DisplayFa('تغییر یافته توسط مسافر')]
    case TicketedChanged = 22;

    #[DisplayFa('تغییر زمان پرواز توسط ایرلاین')]
    case TicketedScheduleChange = 23;

    #[DisplayFa('باطل شده')]
    case TicketedCancelled = 24;

    #[DisplayFa('باطل شده')]
    case TicketedVoid = 25;

    #[DisplayFa('باطل شده')]
    case Cancelled = 30;

    #[DisplayFa('ناموفق')]
    case Exception = 40;

    #[DisplayFa('ناموفق')]
    case Gateway = 41;

    #[DisplayFa('ناموفق')]
    case Duplicate = 42;

    public function getDescription()
    {
        return $this->getAttributeValue(DisplayFa::class);
    }
}