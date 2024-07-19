<?php

namespace App\Traits;

use ReflectionEnumUnitCase;

trait EnumAttributeCatcher
{
    public function getAttributeValue(string $attribute)
    {
        return (new ReflectionEnumUnitCase(self::class, $this->name))->getAttributes($attribute)[0]->getArguments()[0];
    }
}