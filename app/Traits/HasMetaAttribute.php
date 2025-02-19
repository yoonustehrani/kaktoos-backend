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

    public function addToMeta(string $key, mixed $value)
    {
        $meta = $this->meta; // Get the current meta data
        data_set($meta, $key, $value); // Modify the meta data
        $this->meta = $meta; // Set the modified meta data back
    }
}