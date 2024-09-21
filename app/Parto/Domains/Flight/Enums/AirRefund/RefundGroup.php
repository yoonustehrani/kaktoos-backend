<?php

namespace App\Parto\Domains\Flight\Enums\AirRefund;

enum RefundGroup: int {
    case Eticket = 1;
    case Segment  = 2;
    case Pnr = 3;
}