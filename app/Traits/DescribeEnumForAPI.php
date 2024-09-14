<?php

namespace App\Traits;

use App\Attributes\DisplayFa;

trait DescribeEnumForAPI
{
    public static function describe()
    {
        return array_map(fn($case) => [
            'name' => $case->name,
            'value' => $case->value,
            'name_fa' => $case->getAttributeValue(DisplayFa::class)
        ], self::cases());
    }
}
