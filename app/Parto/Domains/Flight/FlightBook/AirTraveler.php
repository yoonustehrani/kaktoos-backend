<?php

namespace App\Parto\Domains\Flight\FlightBook;

use App\Parto\Domains\Flight\Enums\PartoPassengerGender;
use App\Parto\Domains\Flight\Enums\PartoPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\Enums\TravellerTitle;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AirTraveler
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
                'PassportNumber' => null,
                'ExpiryDate' => null
            ],
            'NationalId' => ''
        ]);
    }

    public static function make()
    {
        return new self();
    }

    protected function configureTitle()
    {
        $title = null;
        $is_male = TravellerGender::{PartoPassengerGender::tryFrom($this->attributes->get('Gender'))->name} === TravellerGender::Male;
        switch (PartoPassengerType::tryFrom($this->attributes->get('PassengerType'))->name) {
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

    protected function configure(): Collection
    {
        $this->configureTitle();
        $attrs = $this->attributes->undot();
        // if (is_null($this->attributes['Passport']['PassportNumber'])) {
        //     $attrs->put('Passport', []);
        // }
        return $attrs;
    }

    public function setPassengerType(TravellerPassengerType $type)
    {
        $this->attributes->put('PassengerType', PartoPassengerType::{$type->name}->value);
        return $this;
    }

    public function setGender(TravellerGender $gender)
    {
        $this->attributes->put('Gender', PartoPassengerGender::{$gender->name}->value);
        return $this;
    }

    public function setName(string $firstName, string $middleName, string $lastName)
    {
        $this->attributes->put('PassengerName', [
            'PassengerFirstName' => $firstName,
            'PassengerMiddleName' => $middleName,
            'PassengerLastName' => $lastName
        ]);
        return $this;
    }

    public function setSeatPreference(TravellerSeatPreference $preference)
    {
        $this->attributes->put('SeatPreference', $preference->name);
        return $this;
    }

    public function hasWheelchair()
    {
        $this->attributes->put('Wheelchair', true);
        return $this;
    }

    public function setNationality(string $countryCode)
    {
        $this->attributes->put('Nationality', $countryCode);
        $this->attributes->put('Passport.Country', $countryCode);
        return $this;
    }

    public function setPassport(string $passportNumber, Carbon $expires_on, Carbon $issued_on = null)
    {
        $this->attributes->put('Passport.PassportNumber', $passportNumber);
        $this->attributes->put('Passport.ExpiryDate', $expires_on->format($this->datetime_formate));
        if (! is_null($issued_on)) {
            $this->attributes->put('Passport.IssueDate', $issued_on->format($this->datetime_formate));
        }
        return $this;
    }

    public function setNationalId(string $national_id)
    {
        $this->attributes->put('NationalId', $national_id);
        return $this;
    }

    public function setBirthdate(Carbon $birthdate)
    {
        $this->attributes->put('DateOfBirth', $birthdate->format($this->datetime_formate));
        return $this;
    }

    public function get()
    {
        return $this->configure()->toArray();
    }
}