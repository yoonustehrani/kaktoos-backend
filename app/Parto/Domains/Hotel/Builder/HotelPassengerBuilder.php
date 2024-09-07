<?php

namespace App\Parto\Domains\Hotel\Builder;

use App\Parto\Domains\Flight\Enums\TravellerGender;

class HotelPassengerBuilder extends QueryBuilder
{
    protected function __construct(array $initial_data)
    {
        $this->query = array_merge([
            'FirstName' => null,
            'LastName' => null,
            'PassengerType' => 'Adt',
            'PassengerTitle' => null,
            'Gender' => null,
            'ChildAge' => null,
            'NationalId' => null,
            'PassportNumber' => null
        ], $initial_data);
    }

    public function setPassportNumber(string $passport_number)
    {
        return $this->set('PassportNumber', $passport_number);
    }

    public function setNationalId(string $national_id)
    {
        return $this->set('NationalId', $national_id);
    }

    public function setName(string $firstName, string $lastName)
    {
        $this->set('FirstName', $firstName);
        return $this->set('LastName', $lastName);
    }

    public static function child(int $age, TravellerGender $gender)
    {
        return new self([
            'PassengerTitle' => TravellerGender::Male == $gender ? 'Mstr' : 'Miss',
            'ChildAge' => $age,
            'PassengerType' => 'Chd'
        ]);
    }

    public static function adult(TravellerGender $gender)
    {
        return new self([
            'PassengerTitle' => TravellerGender::Male == $gender ? 'Mr' : 'Ms',
            'PassengerType' => 'Adt'
        ]);
    }
}