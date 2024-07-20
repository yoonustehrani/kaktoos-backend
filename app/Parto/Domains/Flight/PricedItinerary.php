<?php

namespace App\Parto\Domains\Flight;

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

    public function getLastFlightSegment()
    {
        return Carbon::createFromFormat(
            format: 'Y-m-d\TH:i:s',
            time: Arr::last(
                Arr::last($this->result->get('OriginDestinationOptions'))['FlightSegments']
            )['ArrivalDateTime']
        );
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
}