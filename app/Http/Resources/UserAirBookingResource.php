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
        /**
         * @var \App\Parto\Enums\HotelQueueStatus $status
         */
        $status = $this['status'];
        return [
            'id' => $this['id'],
            'type' => $this['type'],
            'is_webfare' => $this['is_webfare'],
            'created_at' => $this['created_at'],
            'updated_at' => $this['updated_at'],
            'status' => $this['status'],
            'status_fa' => $status->getAttributeValue(DisplayFa::class),
            'order_paid_at' => $this['order']['paid_at'],
            'flights' => $this['flights'],
            'ticket_url' => $status == AirQueueStatus::Ticketed ? route('bookings.air.tickets.index', ['airBooking' => $this['id']]) : null
        ];
    }
}
