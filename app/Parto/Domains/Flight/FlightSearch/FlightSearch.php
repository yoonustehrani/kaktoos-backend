<?php

namespace App\Parto\Domains\Flight\FlightSearch;

use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\AirSearch\AirTripType;
use App\Parto\Domains\Flight\Enums\AirSearch\MaxStopsQuantity;
use App\Parto\Domains\Flight\Enums\AirSearch\PartoCabinType;
use App\Parto\Domains\Flight\Enums\AirSearch\PricingSourceType;
use App\Parto\Domains\Flight\Enums\AirSearch\RequestOption;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FlightSearch
{
    public array $attributes = [];
    const TRAVEL_INFO_KEY = 'TravelPreference';
    const ORIGING_DESTINATION_KEY = 'OriginDestinationInformations';

    public function __construct()
    {
        $this->setAttribute('PricingSourceType', PricingSourceType::All->value);
        $this->setAttribute('RequestOption', RequestOption::All->value);
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
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.CabinType', PartoCabinType::{$cabin->name}->value);
        return $this;
    }
    public function oneWay(FlightOriginDestination $data)
    {
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.MaxStopsQuantity', MaxStopsQuantity::All->value);
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.AirTripType', AirTripType::OneWay->value);
        foreach ($data->toArray() as $key => $value) {
            $this->setOriginDistinationData("0.$key", $value);
        }
        return $this;
    }
    public function roundtrip(FlightOriginDestination $first, FlightOriginDestination $second)
    {
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.MaxStopsQuantity', 'All');
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.AirTripType', AirTripType::Return->value);
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
        $this->setAttribute(self::TRAVEL_INFO_KEY . '.AirTripType', AirTripType::Circle->value);
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