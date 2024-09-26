<?php

namespace App;

use App\Attributes\DisplayFa;
use App\Traits\DescribeEnumForAPI;
use App\Traits\EnumAttributeCatcher;

enum CreditAction: int
{
    use EnumAttributeCatcher, DescribeEnumForAPI;

    #[DisplayFa('افزایش موجودی')]
    case Increase = 1;

    #[DisplayFa('کسر از موجودی')]
    case Decrease = 2;
}
