<?php

namespace App\Http\Resources;

use App\Attributes\DisplayFa;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AirBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var \App\Parto\Enums\HotelQueueStatus $status
         */
        $status = $this['status'];
        return array_merge(parent::toArray($request), [
            'status' => str($status->name)->kebab(),
            'status_fa' => $status->getAttributeValue(DisplayFa::class)
        ]);
    }
}
