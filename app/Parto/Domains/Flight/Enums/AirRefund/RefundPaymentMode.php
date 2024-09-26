<?php

namespace App\Parto\Domains\Flight\Enums\AirRefund;

enum RefundPaymentMode: int {
    case UnKnown = 0;
    case Credit  = 1;
    case BankAccount = 2;
}