<?php

namespace App\Http\Resources;

use App\Traits\HasMetaResourceAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartoBaggageRuleResource extends JsonResource
{
    use HasMetaResourceAttribute;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'from_airport' => $this['Departure'],
            'to_airport' => $this['Arrival'],
            'flight_number' => $this['FlightNo'],
            'baggage' => $this['Baggage']
        ];
    }
}