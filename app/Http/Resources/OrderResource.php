<?php

namespace App\Http\Resources;

use App\Attributes\DisplayFa;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var \App\Enums\TransactionStatus
         */
        $status = $this['status'];
        return [
            'id' => $this['id'],
            'status' => str($status)->kebab(),
            'status_fa' => $status->getAttributeValue(DisplayFa::class)
        ];
    }
}
