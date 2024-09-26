<?php

namespace App\Parto\Domains\Flight\Enums;

use App\Attributes\DisplayFa;
use App\Traits\DescribeEnumForAPI;
use App\Traits\EnumAttributeCatcher;

enum PartoRefundMethod: int {
    use EnumAttributeCatcher, DescribeEnumForAPI;

    #[DisplayFa('آفلاین')]
    case Offline = 0;

    #[DisplayFa('آنلاین')]
    case Online = 1;

    #[DisplayFa('غیرقابل استرداد')]
    case NonRefundable = 2;
}