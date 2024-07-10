<?php

namespace App\Parto\Domains\Flight\FlightBook;

use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\Enums\TravellerTitle;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AirTraveller
{
    protected Collection $attributes;
    public string $datetime_formate;

    protected function __construct()
    {
        $this->datetime_formate = config('services.parto.datetime_format');
        $this->attributes = collect([
            'ExtraServiceId' => null,
            'MealTypeServiceId' => null,
            'SeatServiceId' => null,
            'FrequentFlyerNumber' => null,
            'DestinationAddress' => null,
            'Passport' => [
                'Country' => null,
                'IssueDate' => null,
                'PassportNumber' => null
            ]
        ]);
    }

    public static function make()
    {
        return new self();
    }

    protected function configureTitle()
    {
        $title = null;
        $is_male = $this->attributes->get('gender') === TravellerGender::Male->name;
        switch ($this->attributes->get('PassengerType')) {
            case TravellerPassengerType::Adt->name:
                $title = $is_male ? TravellerTitle::Mr : TravellerTitle::Ms;
                break;
            case TravellerPassengerType::Chd->name:
            case TravellerPassengerType::Inf->name:
                $title = $is_male ? TravellerTitle::Mstr : TravellerTitle::Miss;
                break;
        }
        if (! $title instanceof TravellerTitle) {
            throw new Exception('Traveller title not specified');
        }
        $this->attributes->put('PassengerName.PassengerTitle', $title->name);
    }

    protected function configure()
    {
        $this->configureTitle();
    }

    public function setPassengerType(TravellerPassengerType $type)
    {
        $this->attributes->put('PassengerType', $type->name);
    }

    public function setGender(TravellerGender $gender)
    {
        $this->attributes->put('Gender', $gender->name);
    }

    public function setName(string $firstName, string $middleName, string $lastName)
    {
        $this->attributes->put('PassengerName', [
            'PassengerFirstName' => $firstName,
            'PassengerMiddleName' => $middleName,
            'PassengerLastName' => $lastName
        ]);
    }

    public function setSeatPreference(TravellerSeatPreference $preference)
    {
        $this->attributes->put('PassengerType', $preference->name);
    }

    public function hasWheelchair()
    {
        $this->attributes->put('Wheelchair', true);
    }

    public function setNationality(string $countryCode)
    {
        $this->attributes->put('Nationality', $countryCode);
        $this->attributes->put('Passport.Country', $countryCode);
    }

    public function setPassport(string $passportNumber, Carbon $expires_on, Carbon $issued_on)
    {
        $this->attributes->put('Passport.PassportNumber', $passportNumber);
        $this->attributes->put('Passport.ExpiryDate', $expires_on->format($this->datetime_formate));
        $this->attributes->put('Passport.IssueDate', $expires_on->format($this->datetime_formate));
    }

    public function setBirthdate(Carbon $birthdate)
    {
        $this->attributes->put('DateOfBirth', $birthdate->format($this->datetime_formate));
    }

    public function get()
    {
        return $this->attributes->toArray();
    }
}