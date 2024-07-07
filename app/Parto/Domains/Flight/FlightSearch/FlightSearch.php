<?php

namespace App\Parto\Domains\Flight\FlightSearch;

use App\Parto\Domains\Flight\Enums\AirTripType;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FlightSearch
{
    public array $attributes = [];
    const TRAVEL_INFO_KEY = 'TravelPreference';
    const ORIGING_DESTINATION_KEY = 'OriginDestinationInformations';

    public function __construct()
    {
        $this->setAttribute('PricingSourceType', 'All');
        $this->setAttribute('RequestOption', 'All');
    }
    protected function setAttribute(string $attr, mixed $value)
    {
        Arr::set($this->attributes, $attr, $value);
    }
    public function setCount(int $adult = 1, int $child = 0, int $infant = 0)
    {
        foreach (compact('adult', 'child', 'infant') as $key => $count) {
            $key = Str::ucfirst($key);
            $this->setAttribute("{$key}Count", $count);
        }
        return $this;
    }
    protected function setOriginDistinationData(string $key, string $value)
    {
        $this->setAttribute(self::ORIGING_DESTINATION_KEY . '.' . $key, $value);
    }
    /**
     * @param FlightCabinType|null $cabin
     */
    public function setCabinType(FlightCabinType|null $cabin = null)
    {
        if (is_null($cabin)) {
            $cabin = FlightCabinType::Default;
        }
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.CabinType', $cabin->name);
        return $this;
    }
    public function oneWay(FlightOriginDestination $data)
    {
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.MaxStopsQuantity', 'All');
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.AirTripType', AirTripType::OneWay->name);
        foreach ($data->toArray() as $key => $value) {
            $this->setOriginDistinationData("0.$key", $value);
        }
        return $this;
    }
    public function roundtrip(FlightOriginDestination $first, FlightOriginDestination $second)
    {
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.MaxStopsQuantity', 'All');
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.AirTripType', AirTripType::Return->name);
        foreach ($first->toArray() as $key => $value) {
            $this->setOriginDistinationData("0.$key", $value);
        }
        foreach ($second->toArray() as $key => $value) {
            $this->setOriginDistinationData("1.$key", $value);
        }
        return $this;
    }
    public function multi(FlightOriginDestination $first, FlightOriginDestination ...$data)
    {
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.AirTripType', AirTripType::Circle->name);
        return $this;
    }
    /**
     * @param string[] $airlines
     */
    public function excludeAirlines(array $airlines)
    {
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.VendorExcludeCodes', $airlines);
    }
    /**
     * @param string[] $airlines
     */
    public function includeAirlines(array $airlines)
    {
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.VendorPreferenceCodes', $airlines);
    }
    public function getQuery()
    {
        return $this->attributes;
    }
}