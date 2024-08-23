<?php

namespace App\Parto\Traits;

trait PartoHotelMethods
{
    public function searchHotels(array $query)
    {
        return $this->apiCall('Hotel/HotelAvailability', $query);
    }
}