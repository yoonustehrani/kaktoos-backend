<?php

namespace App\Parto\Domains\Hotel\Builder;

use App\Parto\Parto;
use Exception;
use Illuminate\Support\Carbon;

class HotelSearchQueryBuilder extends QueryBuilder
{
    public function __construct(array $initialData)
    {
        $default = [
            'Latitude' => null,
            'Longitude' => null,
            'RadiusInKilometer' => 0,
            'SetGeoLocation' => false,
            'HotelId' => null,
            'HotelIdList' => null,
            'CityId' => null,
            'RegionCode' => null,
            'CountryCode' => null,
            'Occupancies' => [],
            'NationalityId' => 'IR'
        ];
        $this->query = array_merge($default, $initialData);
    }

    public function setDates(string $checkIn, string $checkOut)
    {
        foreach (compact('checkIn', 'checkOut') as $key => $dateString) {
            $this->set(ucfirst($key), Carbon::createFromFormat('Y-m-d', $dateString)->format(Parto::DATETIME_FORMAT));
        }
        return $this;
    }

    public function addRoom(int $adultCount, int $childCount = 0, array $childAges = [])
    {
        if (count($childAges) != $childCount) {
            throw new Exception('Age for all children should be specified');
        }
        $a = [];
        foreach (compact('adultCount', 'childCount', 'childAges') as $key => $value) {
            $a[ucfirst($key)] = $value;
        }
        $this->query['Occupancies'][] = $a;
        return $this;
    }
}