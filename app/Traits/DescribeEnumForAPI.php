<?php

namespace App\Traits;

use App\Attributes\DisplayFa;

trait DescribeEnumForAPI
{
    public function getNameForApi()
    {
        return str($this->name)->kebab()->lower();
    }
    public static function describe()
    {
        return array_map(fn($case) => [
            'name' => $case->getNameForApi(),
            'value' => $case->value,
            'name_fa' => $case->getAttributeValue(DisplayFa::class)
        ], self::cases());
    }
}
