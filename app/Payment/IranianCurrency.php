<?php

namespace App\Payment;

use App\Attributes\DisplayFa;
use App\Traits\EnumAttributeCatcher;

enum IranianCurrency: string {
    use EnumAttributeCatcher;

    #[DisplayFa('ریال')]
    case RIAL = 'IRR';

    #[DisplayFa('تومان')]
    case TOMAN = 'IRT';

    public function getDisplayFa()
    {
        return $this->getAttributeValue(DisplayFa::class);
    }
}