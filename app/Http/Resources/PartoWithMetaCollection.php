<?php

namespace App\Http\Resources;

use App\Traits\HasMetaResourceAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PartoWithMetaCollection extends ResourceCollection
{
    use HasMetaResourceAttribute;
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
