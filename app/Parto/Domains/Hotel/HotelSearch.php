<?php

namespace App\Parto\Domains\Hotel;

use App\Parto\Domains\Hotel\Builder\HotelSearchQueryBuilder;

class HotelSearch
{
    public function searchByHotelId(int $hotelId)
    {
        return new HotelSearchQueryBuilder([
            'HotelId' => $hotelId,
        ]);
    }

    public function searchByCityId(int $cityId,)
    {
        return new HotelSearchQueryBuilder([
            'CityId' => $cityId,
        ]);
    }
}