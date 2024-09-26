<?php

namespace App\Enums;

use App\Attributes\DisplayFa;
use App\Traits\EnumAttributeCatcher;

enum VerificationResultStatus
{
    use EnumAttributeCatcher;
    
    #[DisplayFa('موفق')]
    case SUCCESSFUL;

    #[DisplayFa('ناموفق')]
    case FAILED;

    #[DisplayFa('بازگشت داده شده')]
    case REVERSED;

    #[DisplayFa('نامشخص')]
    case UNKNOWN;

    #[DisplayFa('قبلا تایید شده')]
    case ALREADY_VERIFIED;

    #[DisplayFa('هنوز تراکنشی انچام نشده')]
    case NOT_VERIFIABLE;

    public function getDisplayFa()
    {
        return $this->getAttributeValue(DisplayFa::class);
    }
}
