<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelBookingRequest;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Hotel\Builder\HotelBookingQueryBuilder;
use App\Parto\Domains\Hotel\Builder\HotelPassengerBuilder;
use App\Parto\Facades\Parto;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    public function store(HotelBookingRequest $request)
    {
        $booking = new HotelBookingQueryBuilder($request->user());
        foreach ($request->input('rooms') as $room) {
            $room = HotelBookingQueryBuilder::newRoom();
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
            }
            $booking->addRoom($room);
        }
        return Parto::api()->bookHotel($request->input('ref'), $booking);
    }
}
