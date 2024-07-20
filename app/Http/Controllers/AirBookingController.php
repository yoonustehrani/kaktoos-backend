<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightBookingRequest;
use App\Models\AirBooking;
use App\Models\Order;
use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Domains\Flight\Enums\PartoFareType;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\FlightBook\AirTraveler;
use App\Parto\Parto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AirBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlightBookingRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $airBook = Parto::flight()->flightBook();
        $airBook->setFareCode($request->input('ref'))
            ->setPhoneNumber($user->phone_number)
            ->setEmail('yoonustehrani28@gmail.com');

        foreach ($request->input('passengers') as $passenger) {
            $t = AirTraveler::make()
                ->setName(firstName: $passenger['first_name'], middleName: $passenger->middle_name ?? '', lastName: $passenger['last_name'])
                ->setGender(TravellerGender::tryFrom($passenger['gender']))
                ->setBirthdate(Carbon::createFromFormat('Y-m-d', $passenger['birthdate']))
                ->setNationality($passenger['nationality'])
                ->setPassengerType(TravellerPassengerType::tryFrom($passenger['type']))
                ->setSeatPreference(TravellerSeatPreference::tryFrom('any'));
            if ($request->revalidated_flight->isPassportMandatory()) {
                $t->setPassport(
                    passportNumber: $passenger['passport']['passport_number'],
                    expires_on: $passenger['passport']['expiry_date'],
                    issued_on: $passenger['passport']['issue_date']
                );
            } else {
                $t->setNationalId($passenger['national_id']);
            }
            $airBook->addTraveler($t);
            unset($t);
        }
        
        $booking = new AirBooking();
        $booking->is_webfare = $request->revalidated_flight->isWebfare();
        $status = AirQueueStatus::Booked;

        if (! $booking->is_webfare) {
            $result = Parto::flightBook($airBook);
            abort_if(
                AirBookCategory::tryFrom($result->Category) != AirBookCategory::Booked,
                'Couldn\'t book the flight. please try again!',
                500
            );
            $booking->parto_unique_id = $result->UniqueId;
            $status = AirQueueStatus::tryFrom($result->Status);
            try {
                $booking->valid_until = Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $result->TktTimeLimit);
            } catch (\Throwable $th) {
                $booking->valid_until = Carbon::createFromFormat('Y-m-d\TH:i:s', $result->TktTimeLimit);
            }
        } else {
            $booking->valid_until = now()->addMinutes(14);
        }

        $booking->status = $status;
        $booking->status_notes = $status->getDescription();
        
        try {
            DB::beginTransaction();
            $user->airBookings()->save($booking);
            $order = $booking->order()->save(new Order([
                    'user_id' => $user->id,
                    'amount' => $request->revalidated_flight->getTotalInRials()
                ])
            );
            DB::commit();
            return [
                'ticket_time_limit' => $booking->valid_until->format('Y-m-d H:i:s'),
                'price_changed' => $result->PriceChange ?? false,
                'payment' => [
                    'amount' => $request->revalidated_flight->getTotalInRials(),
                    'url' => route('orders.pay', ['order' => $order->id])
                ]
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            // Better to retry
            abort(500, 'Issue in saving order in DB');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AirBooking $airBooking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AirBooking $airBooking)
    {
        //
    }
}
