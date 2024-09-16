<?php

namespace App\Http\Resources;

use App\Attributes\DisplayFa;
use App\Models\AirBooking;
use App\Models\Parto\Hotel\HotelBooking;
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
        switch ($this['purchasable_type']) {
            case AirBooking::class:
                $purchasable = 'flight';
                $purchasable_url = route('user.bookings.air.show', ['airBooking' => $this['purchasable_id']], absolute: false);
                break;
            case HotelBooking::class:
                $purchasable = 'hotel';
                $purchasable_url = route('user.bookings.hotel.show', ['hotelBooking' => $this['purchasable_id']], absolute: false);
                break;
            default:
                $purchasable = 'unknown';
                $purchasable_url = null;
                break;
        }
        return array_merge(
            parent::toArray($request),
            [
                'purchasable_type' => $purchasable,
                'purchasable_type_fa' => __(ucfirst($purchasable)),
                "purchasable_url" => $purchasable_url
            ]
        );
    }
}
