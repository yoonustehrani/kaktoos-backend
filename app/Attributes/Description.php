<?php

namespace App\Attributes;

use Attribute;

#[Attribute]
class Description
{
    public function __construct(public string $description) { }
}