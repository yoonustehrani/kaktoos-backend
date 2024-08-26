<?php

namespace App\Parto\Client\Traits;

use App\Parto\Domains\Hotel\Builder\HotelBookingQueryBuilder;
use App\Parto\Domains\Hotel\Builder\HotelCancellationQueryBuilder;
use App\Parto\Domains\Hotel\Builder\HotelSearchQueryBuilder;

/**
 * @method \stdClass apiCall(string $uri, array $data = [], $auth = true)
 */
trait PartoHotelMethods
{
    public function searchHotels(HotelSearchQueryBuilder $query)
    {
        return $this->apiCall('Hotel/HotelAvailability', $query->get());
    }

    public function checkOffer(string $ref)
    {
        return $this->apiCall('Hotel/HotelCheckRate', ['FareSourceCode' => $ref]);
    }

    public function bookHotel(string $ref, HotelBookingQueryBuilder $query)
    {
        return $this->apiCall('Hotel/HotelBook', array_merge(['FareSourceCode' => $ref], $query->get()));
    }

    public function confirmHotelBook(string $uniqueId)
    {
        return $this->apiCall('Hotel/HotelOrder', ['UniqueId' => $uniqueId]);
    }

    public function cancelHotelBook(string $uniqueId, HotelCancellationQueryBuilder $query)
    {
        return $this->apiCall('Hotel/HotelOrder', array_merge(['UniqueId' => $uniqueId], $query->get()));
    }

    public function requestHotelImages(int $hotelId)
    {
        return $this->apiCall('Hotel/HotelImage', ['HotelId' => $hotelId]);
    }

    public function requestHotelImagesBulk(array $hotelIds)
    {
        return $this->apiCall('Hotel/HotelImages', ['HotelId' => $hotelIds]);
    }
}