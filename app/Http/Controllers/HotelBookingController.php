<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelBookingRequest;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Hotel\Builder\HotelPassengerBuilder;
use App\Parto\Facades\Parto;

class HotelBookingController extends Controller
{
    public function store(HotelBookingRequest $request)
    {
        $ref = Parto::api()->hotel()->checkOffer($request->input('ref'))->PricedItinerary['FareSourceCode'];

        $booking = Parto::hotel()->hotelBooking($request->user());
        foreach ($request->input('rooms') as $room) {
            $roomQuery = Parto::hotel()->newHotelRoom();
            foreach ($room['residents'] as $resident) {
                switch (TravellerPassengerType::tryFrom($resident['type'])) {
                    case TravellerPassengerType::Chd:
                        $passenger = HotelPassengerBuilder::child(age: $resident['age'], gender: TravellerGender::tryFrom($resident['gender']));
                        break;
                    default:
                        $passenger = HotelPassengerBuilder::adult(TravellerGender::tryFrom($resident['gender']));
                        break;
                }
                $passenger->setName($resident['first_name'], $resident['last_name']);
                if ($resident['national_id']) {
                    $passenger->setNationalId($resident['national_id']);
                } else {
                    $passenger->setPassportNumber($resident['passport_number']);
                }
                $roomQuery->addPassenger($passenger);
            }
            $booking->addRoom($roomQuery);
        }
        return Parto::api()->hotel()->bookHotel($ref, $booking);
    }
}
