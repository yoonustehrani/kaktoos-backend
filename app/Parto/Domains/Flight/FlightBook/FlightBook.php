<?php

namespace App\Parto\Domains\Flight\FlightBook;

use App\Models\User;
use Illuminate\Support\Collection;

final class FlightBook
{
    public Collection $attributes;
    // const TRAVEL_INFO_KEY = 'TravelPreference';
    // const ORIGING_DESTINATION_KEY = 'OriginDestinationInformations';

    public function __construct()
    {
        $this->attributes = collect([
            'FareSourceCode' => null,
            'ClientUniqueId' => null,
            'MarkupForAdult' => 0,
            'MarkupForChild' => 0,
            'MarkupForInfant' => 0,
            'CancellationGuaranteeId' => null,
            'TravelerInfo' => [
                'PhoneNumber' => null,
                'Email' => null,
                'AirTravelers' => []
            ]
        ]);
    }

    public function setUser(User $user)
    {
        $this->setPhoneNumber($user->phone_number);
        $this->setEmail($user->email);
        return $this->setAttribute('ClientUniqueId', "U{$user->id}");
    }

    public function addTraveler(AirTraveler $traveler)
    {
        $this->setAttribute(
            'TravelerInfo.AirTravelers',
            collect($this->attributes->get('TravelerInfo.AirTravelers', []))->push($traveler->get())->toArray()
        );
        return $this;
    }

    public function setFareCode(string $code)
    {
        $this->setAttribute('FareSourceCode', $code);
        return $this;
    }

    public function setPhoneNumber(string $phone_number)
    {
        $this->setAttribute('TravelerInfo.PhoneNumber', $phone_number);
        return $this;
    }

    public function setEmail(string $email)
    {
        $this->setAttribute('TravelerInfo.Email', $email);
        return $this;
    }

    protected function setAttribute(string $key, mixed $value)
    {
        $this->attributes->put($key, $value);
    }

    public function getQuery()
    {
        return $this->attributes->undot()->toArray();
    }
}