<?php

namespace App\Parto\Domains\Hotel\Builder;

class HotelRoomBuilder extends QueryBuilder
{
    public function __construct()
    {
        $this->query = [
            'Passengers' => [],
            'HotelRoomEarlyCheckin' => null,
            'HotelRoomLateCheckout' => null
        ];
    }

    public function addPassenger(HotelPassengerBuilder $passenger)
    {
        $this->query['Passengers'][] = $passenger->get();
        return $this;
    }
}