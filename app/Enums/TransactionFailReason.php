<?php

namespace App\Enums;

use App\Attributes\DisplayFa;
use App\Traits\DescribeEnumForAPI;
use App\Traits\EnumAttributeCatcher;

enum TransactionFailReason
{
    use EnumAttributeCatcher, DescribeEnumForAPI;

    #[DisplayFa('توسط کاربر لغو شده')]
    case CANCELLED_BY_USER;

    #[DisplayFa('زمان مجاز انجام تراکنش به پایان رسیده است.')]
    case TRANSACTION_TIMED_OUT;

    #[DisplayFa('کارت بانکی در سیستم ثبت نشده است.')]
    case PSP_PAYER_CARD_NOT_MATCHED;

    #[DisplayFa('نامشخص')]
    case UNKNOWN;
}
