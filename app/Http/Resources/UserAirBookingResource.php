<?php

namespace App\Http\Resources;

use App\Attributes\DisplayFa;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAirBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            parent::toArray($request),
            [
                'status' => [
                    'value' => $this['status']->value,
                    'name' => $this['status']->getNameForApi(),
                    'name_fa' => $this['status']->getAttributeValue(DisplayFa::class)
                ],
                'type' => [
                    'value' => $this['type']->value,
                    'name' => $this['type']->getNameForApi(),
                    'name_fa' => $this['type']->getAttributeValue(DisplayFa::class)
                ],
                'refund_type' => [
                    'value' => $this['refund_type']->value,
                    'name' => $this['refund_type']->getNameForApi(),
                    'name_fa' => $this['refund_type']->getAttributeValue(DisplayFa::class)
                ],
                'ticket_url' => $this['status'] == AirQueueStatus::Ticketed ? route('bookings.air.tickets.index', ['airBooking' => $this['id']]) : null
            ]
        );
    }
}
