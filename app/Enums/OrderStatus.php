<?php

namespace App\Enums;

use App\Attributes\DisplayFa;
use App\Traits\DescribeEnumForAPI;
use App\Traits\EnumAttributeCatcher;

enum OrderStatus: int
{
    use EnumAttributeCatcher, DescribeEnumForAPI;
    
    #[DisplayFa('در انتظار پرداخت')]
    case PENDING = 0;

    #[DisplayFa('تکمیل شده')]
    case COMPLETED = 1;

    #[DisplayFa('در حال پردازش')]
    case PROCESSING = 2;

    #[DisplayFa('کنسل شده')]
    case CANCELLED = 3;

    #[DisplayFa('عودت داده شده')]
    case REFUNDED = 4;
}
