<?php

namespace App\Traits;

trait HasMetaResourceAttribute 
{
    public array $meta = [];

    public function withMeta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }
}