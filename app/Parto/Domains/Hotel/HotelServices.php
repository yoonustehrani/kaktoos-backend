<?php

namespace App\Parto\Domains\Hotel;

use App\Models\User;
use App\Parto\Domains\Hotel\Builder\HotelBookingQueryBuilder;

class HotelServices
{
    public function hotelSearch()
    {
        return new HotelSearch();
    }

    public function hotelBooking(User $user)
    {
        return new HotelBookingQueryBuilder($user);
    }

    public function newHotelRoom()
    {
        return HotelBookingQueryBuilder::newRoom();
    }
}