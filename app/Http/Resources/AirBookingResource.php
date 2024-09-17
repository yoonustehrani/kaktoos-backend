<?php

namespace App\Http\Resources;

use App\Attributes\DisplayFa;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
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
        return [
            'id' => $this['id'],
            'type' => $this['type'],
            'type' => [
                'value' => $this['type']->value,
                'name' => $this['type']->getNameForApi(),
                'name_fa' => $this['type']->getAttributeValue(DisplayFa::class)
            ],
            'is_webfare' => $this['is_webfare'],
            'created_at' => $this['created_at'],
            'updated_at' => $this['updated_at'],
            'status' => [
                'value' => $this['status']->value,
                'name' => $this['status']->getNameForApi(),
                'name_fa' => $this['status']->getAttributeValue(DisplayFa::class)
            ],
            'status_notes' => $this['status_notes'],
            'order' => new OrderPlainResource($this['order']),
            'origin_airport' => $this['origin_airport'],
            'destination_airport' => $this['destination_airport'],
            'journey_begins_at' => $this['journey_begins_at'],
            'journey_ends_at' => $this['journey_ends_at'],
            'airline' => $this['airline'],
            'ticket_url' => $this['status'] == AirQueueStatus::Ticketed ? route('bookings.air.tickets.index', ['airBooking' => $this['id']]) : null
        ];
    }
}
