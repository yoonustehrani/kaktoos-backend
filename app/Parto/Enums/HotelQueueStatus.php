<?php

namespace App\Parto\Enums;
use App\Attributes\Description;
use App\Attributes\DisplayFa;
use App\Traits\EnumAttributeCatcher;

enum HotelQueueStatus: int
{
    use EnumAttributeCatcher;

    #[DisplayFa('رزرو شده')]
    case Booked = 10;

    #[DisplayFa('در صف درخواست')]
    case OnRequest = 11;

    #[DisplayFa('در حال بررسی')]
    case Pending   = 12;

    #[DisplayFa('تایید شده')]
    case Confirm   = 20;

    // Confirm-Cancelled
    #[DisplayFa('لغو تایید')]
    case ConfirmCancelled = 21;

    // Pending
    #[DisplayFa('در صف بررسی تایید')]
    case ConfirmPending = 22;

    #[DisplayFa('باطل شده')]
    case Cancelled = 30;

    // Not Booked
    #[DisplayFa('خطا')]
    case Exception = 40;

    #[DisplayFa('معلق')]
    case Gateway   = 41;

    #[DisplayFa('تکراری')]
    case Duplicate = 42;

    // Not Available
    #[DisplayFa('ناموجود')]
    case NotAvailable = 44;

    // Cancellation In Process
    #[DisplayFa('در حال پردازش کنسلی')]
    case CancellationInProcess = 45;

    // Cancel Pending
    #[DisplayFa('در صف بررسی کنسلی')]
    case CancelPending = 46;

    // CxlRequest Sent To Hotel
    #[DisplayFa('درخواست به هتل ارسال شده')]
    case CxlRequestSentToHotel = 47;

    #[DisplayFa('کنسل شده و در صف عودت وجه')]
    case CancelledAndRefundAwaited = 48;
}
