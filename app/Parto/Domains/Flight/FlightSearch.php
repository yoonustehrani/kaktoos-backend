<?php

namespace App\Parto\Domains\Flight;

use App\Parto\Domains\Flight\Enums\AirTripType;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use Illuminate\Support\Arr;

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
            $key = \Str::ucfirst($key);
            $this->setAttribute("{$key}Count", $count);
        }
        return $this;
    }
    protected function setOriginDistinationData(string $key, string $value)
    {
        $this->setAttribute(self::ORIGING_DESTINATION_KEY . '.' . $key, $value);
    }
    /**
     * @param FlightCabinType $cabin
     */
    public function setCabinType(FlightCabinType $cabin = null)
    {
        // $allowed_types = \Cache::rememberForever(
        //     'flight-cabin-types',
        //     fn() => array_map(fn(FlightCabinType $obj) => $obj->name, FlightCabinType::cases())
        // );
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
    // public function __set(string $name, mixed $value)
    // {
    //     return $name;
    // }
}