<?php

namespace App\Parto\Domains\Flight\Enums;

enum PartoNationalityType: int {
    case NationalityDoesNotMatter = 1;
    case ForIranian = 2;
    case ForForeigners = 3;
}