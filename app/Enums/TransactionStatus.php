<?php

namespace App\Enums;

use App\Attributes\DisplayFa;
use App\Traits\DescribeEnumForAPI;
use App\Traits\EnumAttributeCatcher;

enum TransactionStatus :int 
{
    use EnumAttributeCatcher, DescribeEnumForAPI;

    #[DisplayFa('در انتظار پرداخت')]
    case AWAITING = 0;

    #[DisplayFa('در حال پردازش')]
    case PROCESSING = 1;

    #[DisplayFa('موفق')]
    case SUCCESS = 2;

    #[DisplayFa('ناموفق')]
    case FAIL = 3;
}
