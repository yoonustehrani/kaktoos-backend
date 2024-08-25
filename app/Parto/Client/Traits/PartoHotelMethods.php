<?php

namespace App\Parto\Client\Traits;

trait PartoHotelMethods
{
    public function searchHotels(array $query)
    {
        return $this->apiCall('Hotel/HotelAvailability', $query);
    }

    public function requestHotelImages(int $hotelId)
    {
        return $this->apiCall('Hotel/HotelImage', ['HotelId' => $hotelId]);
    }
}