<?php

namespace App\Http\Resources;

use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\PartoCabinType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightJourneyResource extends JsonResource
{
    public function getAirline($code) {
        return session('airlines')[$code] ?? compact('code');
    }
    public function getAirport($IATA_code) {
        return session('airports')[$IATA_code] ?? compact('IATA_code');
    }
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
                    'from_airport' => $this->getAirport($item['DepartureAirportLocationCode']),
                    'to_airport' => $this->getAirport($item['ArrivalAirportLocationCode']),
                    'departure_datetime' => $item['DepartureDateTime'],
                    'arrival_datetime' => $item['ArrivalDateTime'],
                    'duration' => $item['JourneyDuration'],
                    'duration_in_minutes' => $item['JourneyDurationPerMinute'],
                    'connection_time_in_minutes' => $item['ConnectionTimePerMinute'],
                    'flight_number' => $item['FlightNumber'],
                    'operating_airline' => $this->getAirline($item['OperatingAirline']['Code']),
                    'marketing_airline' => $this->getAirline($item['MarketingAirlineCode']),
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
