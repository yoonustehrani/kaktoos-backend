<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasMetaAttribute
{
    public function meta(): Attribute
    {
        return new Attribute(
            get: fn() => json_decode($this->attributes['meta']),
            set: fn($value) => json_encode($value)
        );
    }
}