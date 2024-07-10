<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FlightFareRuleCollection extends ResourceCollection
{
    public array $meta = [];

    public function withMeta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => $this->meta
        ];
    }
}
