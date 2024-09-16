<?php

namespace App\Parto\Domains\Flight;

use App\Models\Parto\Air\Flight;
use App\Parto\Domains\Flight\Enums\AirSearch\PartoCabinType;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\PartoFareType;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class PricedItinerary
{
    protected $result;
    public function __construct(array $result) {
        $this->result = collect($result);
    }

    public function get(string $key)
    {
        return $this->result->get($key);
    }

    public function getFirstItinirary()
    {
        return Arr::first($this->result->get('OriginDestinationOptions'));
    }

    // public function getLastItinirary()
    // {
    //     return Arr::first($this->result->get('OriginDestinationOptions'));
    // }

    public function getLastFlightSegment()
    {
        return Arr::last(
            Arr::last($this->result->get('OriginDestinationOptions'))['FlightSegments']
        );
    }

    public function getFirstFlightSegment()
    {
        return Arr::first(
            Arr::first($this->result->get('OriginDestinationOptions'))['FlightSegments']
        );
    }

    public function getLastFlightSegmentTime()
    {
        return Carbon::createFromFormat(
            format: 'Y-m-d\TH:i:s',
            time: $this->getLastFlightSegment()['ArrivalDateTime']
        );
    }

    public function getFirstFlightSegmentTime()
    {
        return Carbon::createFromFormat(
            format: 'Y-m-d\TH:i:s',
            time: $this->getFirstFlightSegment()['DepartureDateTime']
        );
    }

    public function getFareSourceCode()
    {
        return $this->get('FareSourceCode');
    }
    public function getTotalInRials()
    {
        return intval(Arr::get($this->result->toArray(), 'AirItineraryPricingInfo.ItinTotalFare.TotalFare'));
    }
    public function isPassportMandatory(): bool
    {
        return $this->get('IsPassportMandatory');
    }
    public function isPassportIssueDateMandatory(): bool
    {
        return $this->get('IsPassportIssueDateMandatory');
    }
    public function getFareType(): PartoFareType
    {
        return PartoFareType::tryFrom($this->get('AirItineraryPricingInfo')['FareType']);
    }
    public function isWebfare(): bool
    {
        return $this->getFareType() == PartoFareType::WebFare;
    }

    /**
     * @return \App\Models\Flight[]
     */
    public function getFlightsAsFlight(): array
    {
        $flights = [];
        foreach ($this->get('OriginDestinationOptions') as $item) {
            foreach ($item['FlightSegments'] as $flight) {
                array_push($flights, new Flight([
                    'flight_number' => $flight['FlightNumber'],
                    'airline_pnr' => "",
                    'departure_airport_code' => $flight['DepartureAirportLocationCode'],
                    'departure_terminal' => null,
                    'departs_at' => get_carbon_datetime($flight['DepartureDateTime'])->format('Y-m-d H:i:s'),
                    'arrival_airport_code' => $flight['ArrivalAirportLocationCode'],
                    'arrival_terminal' => null,
                    'arrives_at' => get_carbon_datetime($flight['ArrivalDateTime'])->format('Y-m-d H:i:s'),
                    'marketing_airline_code' => $flight['MarketingAirlineCode'], 
                    'operating_airline_code' => $flight['OperatingAirline']['Code'],
                    'is_return' => $flight['IsReturn'],
                    'meta' => [
                        'airplane' => [
                            'name' => trim($flight['OperatingAirline']['Equipment'] . ' ' . $flight['OperatingAirline']['EquipmentName']),
                            'cabin_type' => FlightCabinType::{PartoCabinType::tryFrom($flight['CabinClassCode'])->name}->value
                        ],
                        'fare_class' => FlightCabinType::tryFrom(str($flight['ResBookDesigCode'])->kebab()->lower())?->name ?? $flight['ResBookDesigCode'],
                        'baggage' => $flight['Baggage'],
                        'journey' => [
                            'duration' => $flight['JourneyDuration'],
                            'duration_in_minutes' => $flight['JourneyDurationPerMinute']
                        ],
                        'stops' => array_map(fn($stop) => [
                            'airport_code' => $stop['ArrivalAirport'],
                            'arrives_at' => get_carbon_datetime($stop['ArrivalDateTime'])->format('Y-m-d H:i:s'),
                            'departs_at' => get_carbon_datetime($stop['DepartureDateTime'])->format('Y-m-d H:i:s')
                        ], $flight['TechnicalStops']),
                        'is_charter' => $flight['IsCharter'],
                    ]
                ]));
            }
        }
        return $flights;
    }
}