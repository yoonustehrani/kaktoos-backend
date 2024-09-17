<?php

namespace App\Parto\Domains\Flight\Enums\AirSearch;

use App\Attributes\DisplayFa;
use App\Traits\DescribeEnumForAPI;
use App\Traits\EnumAttributeCatcher;

enum AirTripType: int {
    use EnumAttributeCatcher, DescribeEnumForAPI;

    #[DisplayFa('یک طرفه')]
    case OneWay  = 1;

    #[DisplayFa('رفت و برگشت')]
    case Return  = 2;

    #[DisplayFa('چند مقصده و برگشت')]
    case Circle  = 3;

    #[DisplayFa('چند مقصده آزاد')]
    case OpenJaw = 4;
}