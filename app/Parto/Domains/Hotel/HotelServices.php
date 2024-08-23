<?php

namespace App\Parto\Domains\Hotel;

class HotelServices
{
    public function hotelSearch()
    {
        return new HotelSearch();
    }
}