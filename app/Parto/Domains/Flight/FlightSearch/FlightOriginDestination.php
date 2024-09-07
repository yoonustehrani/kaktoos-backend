<?php

namespace App\Parto\Domains\Flight\FlightSearch;

use App\Parto\Domains\Flight\Enums\AirSearch\OriginDestinationType;
use App\Parto\Domains\Flight\Enums\FlightLocationType;

class FlightOriginDestination
{
    public function __construct(
        protected string $originCode,
        protected FlightLocationType $originLocationType,
        protected string $destinationCode,
        protected FlightLocationType $destinationLocationType,
        protected \Illuminate\Support\Carbon $departureDateTime
    ){ }

    public function getFormattedDepartureDateTime ()
    {
        return $this->departureDateTime->format('Y-m-d\TH:i:s.uP');
    }

    public function toArray()
    {
        return [
            'DepartureDateTime' => $this->getFormattedDepartureDateTime(),
            'OriginLocationCode' => $this->originCode,
            'OriginType' => OriginDestinationType::{$this->originLocationType->name}->value,
            'DestinationLocationCode' => $this->destinationCode,
            'DestinationType' => OriginDestinationType::{$this->destinationLocationType->name}->value
        ];
    }
    public function toObject()
    {
        return (object) $this->toArray();
    }
}