<?php

namespace App\Parto\Domains\Hotel\Builder;

use Illuminate\Support\Arr;

class QueryBuilder
{
    protected $query = [];

    protected function set(string $key, mixed $value)
    {
        Arr::set($this->query, $key, $value);
        return $this;
    }

    public function get(): array
    {
        return Arr::undot($this->query);
    }
}