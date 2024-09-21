<?php

namespace App\Parto\Client;

use App\Parto\Client\PartoClient;
use App\Parto\Domains\Flight\Enums\AirRefund\RefundGroup;
use App\Parto\Domains\Flight\Enums\AirRefund\RefundPaymentMode;
use App\Parto\Domains\Flight\FlightBook\FlightBook;
use App\Parto\Domains\Flight\FlightSearch\FlightSearch;
use Illuminate\Support\Carbon;
use stdClass;

class PartoAir extends PartoClient
{
    public function searchFlight(FlightSearch $flightSearch): stdClass|null
    {
        return $this->apiCall('Air/AirLowFareSearch', $flightSearch->getQuery());
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

    public function orderTicket(string $unique_id)
    {
        return $this->apiCall('Air/AirOrderTicket', ['UniqueId' => $unique_id]);
    }

    public function onlineRefund(string $unique_id, RefundGroup $refundGroup = RefundGroup::Pnr, ?array $ticket_numbers = null)
    {
        return $this->apiCall('Air/AirRefund', [
            'UniqueId' => $unique_id,
            'RefundType' => $refundGroup->value,
            'RefundPaymentMode' => RefundPaymentMode::Credit,
            'EticketNumbers' => $ticket_numbers
        ]);
    }

    public function offlineRefund(string $unique_id, ?Carbon $request_date = null, ?array $ticket_numbers = null)
    {
        $request_date = $request_date ?? now();
        return $this->apiCall('Air/AirRefundOfflineRequest', [
            'UniqueId' => $unique_id,
            'RefundPaymentMode' => RefundPaymentMode::Credit,
            'RequestDate' => $request_date->format(config('services.parto.datetime_format')),
            'EticketNumbers' => $ticket_numbers
        ]);
    }
}