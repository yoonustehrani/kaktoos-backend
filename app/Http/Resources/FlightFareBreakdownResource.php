<?php

namespace App\Http\Resources;

use App\Parto\Domains\Flight\Enums\PartoPassengerType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightFareBreakdownResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => str(PartoPassengerType::tryFrom($this['PassengerTypeQuantity']['PassengerType'])->name)->lower(),
            'quantity' => $this['PassengerTypeQuantity']['Quantity'],
            'unit' => [
                'amount' => intval($this['PassengerFare']['TotalFare']),
                'currency' => $this['PassengerFare']['Currency']
            ],
            'total' => [
                'amount' => intval($this['PassengerFare']['TotalFare'] * $this['PassengerTypeQuantity']['Quantity']),
                'currency' => $this['PassengerFare']['Currency']
            ]
        ];
    }
}
