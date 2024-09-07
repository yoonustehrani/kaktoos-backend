<?php

namespace App\Attributes;

use Attribute;

#[Attribute]
class DisplayFa
{
    public function __construct(public string $display_fa) { }
}