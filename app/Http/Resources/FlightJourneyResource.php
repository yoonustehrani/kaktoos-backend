<?php

namespace App\Http\Resources;

use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\PartoCabinType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightJourneyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'duration_in_minutes' => $this['JourneyDurationPerMinute'],
            'connection_time_in_minutes' => $this['ConnectionTimePerMinute'],
            'journey' => array_map(function($item) {
                return [
                    'is_charter' => $item['IsCharter'],
                    'is_return' => $item['IsReturn'],
                    'from_airport' => $item['DepartureAirportLocationCode'],
                    'to_airport' => $item['ArrivalAirportLocationCode'],
                    'departure_datetime' => $item['DepartureDateTime'],
                    'arrival_datetime' => $item['ArrivalDateTime'],
                    'duration' => $item['JourneyDuration'],
                    'duration_in_minutes' => $item['JourneyDurationPerMinute'],
                    'connection_time_in_minutes' => $item['ConnectionTimePerMinute'],
                    'flight_number' => $item['FlightNumber'],
                    'operating_airline' => [
                        'code' => $item['OperatingAirline']['Code']
                    ],
                    'marketing_airline' => [
                        'code' => $item['MarketingAirlineCode']
                    ],
                    'airplane' => [
                        'name' => trim($item['OperatingAirline']['Equipment'] . ' ' . $item['OperatingAirline']['EquipmentName']),
                        'cabin_type' => FlightCabinType::{PartoCabinType::tryFrom($item['CabinClassCode'])->name}
                    ],
                    'baggage' => $item['Baggage'],
                    'seats_remaining' => $item['SeatsRemaining'],
                    'stops' => $item['StopQuantity']
                ];
            }, $this['FlightSegments'])
        ];
    }
}
