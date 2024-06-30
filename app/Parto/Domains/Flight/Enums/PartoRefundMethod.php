<?php

namespace App\Parto\Domains\Flight\Enums;

enum PartoRefundMethod: int {
    case Online = 0;
    case Offline = 1;
    case NonRefundable = 2;
}