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
                'ticket_url' => $this['status'] == AirQueueStatus::Ticketed ? route('bookings.air.tickets.index', ['airBooking' => $this['id']]) : null
            ]
        );
    }
}
