<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightBookingRequest;
use App\Http\Resources\AirBookingResource;
use App\Http\Resources\UserAirBookingCollection;
use App\Jobs\InsertTicketData;
use App\Models\AirBooking;
use App\Models\ETicket;
use App\Models\Order;
use App\Models\Parto\Air\Flight;
use App\Models\Passenger;
use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Domains\Flight\Enums\AirSearch\PartoCabinType;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\PartoFareType;
use App\Parto\Domains\Flight\Enums\PartoPassengerGender;
use App\Parto\Domains\Flight\Enums\PartoPassengerType;
use App\Parto\Domains\Flight\Enums\PartoRefundMethod;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\FlightBook\AirTraveler;
use App\Parto\Facades\Parto;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AirBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = get_auth_user()->airBookings()->with([
            'flights', 'order'
        ])->paginate(5);
        return response()->json(
            (new UserAirBookingCollection($orders))->additional([
                'meta' => [
                    'status' => AirQueueStatus::describe(),
                    'refund_type' => PartoRefundMethod::describe()
                ]
            ])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlightBookingRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = $request->user();
        $airBook = Parto::flight()->flightBook();
        $airBook->setFareCode($request->revalidated_flight->getFareSourceCode())
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
            if ($request->revalidated_flight->isPassportMandatory() && isset($passenger['passport'])) {
                $t->setPassport(
                    passportNumber: $passenger['passport']['passport_number'],
                    expires_on: Carbon::createFromFormat('Y-m-d', $passenger['passport']['expiry_date']),
                    issued_on: ! isset($passenger['passport']['issue_date']) ? null : Carbon::createFromFormat('Y-m-d', $passenger['passport']['issue_date'])
                );
            }
            if (isset($passenger['national_id'])) {
                $t->setNationalId($passenger['national_id']);
            }
            $airBook->addTraveler($t);
        }
        $booking = new AirBooking();
        $booking->refund_type = $request->revalidated_flight->get('RefundMethod');
        $booking->is_webfare = $request->revalidated_flight->isWebfare();
        $booking->fill([
            'origin_airport_code' => Arr::first($request->revalidated_flight->getFirstItinirary()['FlightSegments'])['DepartureAirportLocationCode'],
            'destination_airport_code' => Arr::last($request->revalidated_flight->getFirstItinirary()['FlightSegments'])['ArrivalAirportLocationCode'],
            'journey_begins_at' => $request->revalidated_flight->getFirstFlightSegmentTime()->format('Y-m-d H:i:s'),
            'journey_ends_at' => $request->revalidated_flight->getLastFlightSegmentTime()->format('Y-m-d H:i:s'),
            'airline_code' => $request->revalidated_flight->get('ValidatingAirlineCode')
        ]);
        $status = AirQueueStatus::Booked;

        if (! $booking->is_webfare) {
            $result = Parto::api()->air()->flightBook($airBook);
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
            $booking->meta = [
                'webfare' => serialize($airBook)
            ];
        }

        $booking->status = $status;
        
        try {
            DB::beginTransaction();
            $user->airBookings()->save($booking);
            $booking->flights()->saveMany($request->revalidated_flight->getFlightsAsFlight());
            $booking->passengers()->saveMany($request->getPassengersAsPassenger());
            $order = $booking->order()->save(new Order([
                    'title' => __('Flight Ticket'),
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
                    'currency' => 'IRR',
                    'url' => route('orders.pay', ['order' => $order->id])
                ]
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            // Better to retry
            abort(500, 'Issue in saving order in DB');
        }
    }


    
    /**
     * Display the specified resource.
     */
    public function status(AirBooking $airBooking)
    {
        Gate::authorize('view', $airBooking);
        if ($airBooking->parto_unique_id && $airBooking->status != AirQueueStatus::Ticketed) {
            $result = Parto::api()->air()->getBookingDetails($airBooking->parto_unique_id);
        }
        if (isset($result)) {
            try {
                DB::beginTransaction();
                if ($airBooking->parto_unique_id && AirQueueStatus::tryFrom($result->Status) == AirQueueStatus::Ticketed) {
                    InsertTicketData::dispatch($airBooking, $result->TravelItinerary['ItineraryInfo']);
                }
                $airBooking->update([
                    'status' => AirQueueStatus::tryFrom($result->Status)
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        }
        $airBooking->load(['airline', 'origin_airport', 'destination_airport']);
        // $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
        return response()->json(new AirBookingResource($airBooking));
    }

    public function showDetailed(AirBooking $airBooking)
    {
        Gate::authorize('view', $airBooking);
        $airBooking->load(['passengers.tickets', 'flights']);
        $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
        return response()->json(new AirBookingResource($airBooking));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AirBooking $airBooking)
    {
        //
    }
}
