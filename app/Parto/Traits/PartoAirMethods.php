<?php

namespace App\Parto\Traits;

use App\Exceptions\PartoErrorException;
use App\Parto\Domains\Flight\FlightSearch\FlightSearch;
use stdClass;

trait PartoAirMethods
{
    public function searchFlight(FlightSearch $flightSearch): stdClass|null
    {
        try {
            $response = $this->apiCall('Air/AirLowFareSearch', $flightSearch->getQuery());
            return $response;
        } catch (PartoErrorException $error) {
            $errorObject = $error->getErrorObject();
            if ($errorObject->Id === 'Err0103016') {
                return null;
            }
            throw $error;
        }
    }

    public function revalidate(string $fareSourceCode)
    {
        try {
            $response = $this->apiCall('Air/AirRevalidate', ['FareSourceCode' => $fareSourceCode]);

            return $response;
        } catch (PartoErrorException $error) {
            throw $error;
        }
    }

    public function getFareRule(string $fareSourceCode)
    {
        try {
            $response = $this->apiCall('Air/AirRules', ['FareSourceCode' => $fareSourceCode]);

            return $response;
        } catch (PartoErrorException $error) {
            throw $error;
        }
    }

    public function getBaggageRule(string $fareSourceCode)
    {
        try {
            $response = $this->apiCall('Air/AirBaggages', ['FareSourceCode' => $fareSourceCode]);

            return $response;
        } catch (PartoErrorException $error) {
            throw $error;
        }
    }

    public function flightBook()
    {
        
    }
}