<?php

namespace App\Parto\Traits;

use App\Parto\Domains\Flight\FlightBook\FlightBook;
use App\Parto\Domains\Flight\FlightSearch\FlightSearch;
use stdClass;

trait PartoAirMethods
{
    public function searchFlight(FlightSearch $flightSearch): stdClass|null
    {
        return $this->apiCall('Air/AirLowFareSearch', $flightSearch->getQuery());
        // try {
        //     $response = $this->apiCall('Air/AirLowFareSearch', $flightSearch->getQuery());
        //     return $response;
        // } catch (PartoErrorException $error) {
        //     $errorObject = $error->getErrorObject();
        //     if ($errorObject->Id === 'Err0103016') {
        //         return null;
        //     }
        //     throw $error;
        // }
    }

    public function revalidate(string $fareSourceCode)
    {
        return $this->apiCall('Air/AirRevalidate', ['FareSourceCode' => $fareSourceCode]);
    }

    public function getFareRule(string $fareSourceCode)
    {
        return $this->apiCall('Air/AirRules', ['FareSourceCode' => $fareSourceCode]);
    }

    public function getBaggageRule(string $fareSourceCode)
    {
        return $this->apiCall('Air/AirBaggages', ['FareSourceCode' => $fareSourceCode]);
    }

    public function getBookingDetails(string $unique_id)
    {
        return $this->apiCall('Air/AirBookingData', ['UniqueId' => $unique_id]);
    }

    public function cancel(string $unique_id)
    {
        return $this->apiCall('Air/AirCancel', ['UniqueId' => $unique_id]);
    }

    public function flightBook(FlightBook $flightBook)
    {
        return $this->apiCall('Air/AirBook', $flightBook->getQuery());
    }
}