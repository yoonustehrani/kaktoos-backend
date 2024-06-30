<?php

namespace App\Parto\Domains\Flight;

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
            'OriginType' => $this->originLocationType->name,
            'DestinationLocationCode' => $this->destinationCode,
            'DestinationType' => $this->destinationLocationType->name
        ];
    }
    public function toObject()
    {
        return (object) $this->toArray();
    }
}