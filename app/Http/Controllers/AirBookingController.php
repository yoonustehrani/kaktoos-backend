<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightBookingRequest;
use App\Http\Resources\AirBookingResource;
use App\Http\Resources\FlightFareBreakdownResource;
use App\Http\Resources\UserAirBookingCollection;
use App\Http\Resources\UserAirBookingResource;
use App\Jobs\InsertTicketData;
use App\Models\AirBooking;
use App\Models\Order;
use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Domains\Flight\Enums\AirRefund\RefundGroup;
use App\Parto\Domains\Flight\Enums\AirSearch\AirTripType;
use App\Parto\Domains\Flight\Enums\PartoRefundMethod;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\FlightBook\AirTraveler;
use App\Parto\Facades\Parto;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
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
            'order'
        ])->withCount('passengers')->paginate(5);
        return response()->json(
            (new UserAirBookingCollection($orders))->additional([
                'meta' => [
                    'status' => AirQueueStatus::describe(),
                    'refund_type' => PartoRefundMethod::describe(),
                    'type' => AirTripType::describe()
                ]
            ])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlightBookingRequest $request)
    {
        $user = get_auth_user();
        $airBook = Parto::flight()->flightBook();
        $airBook->setFareCode($request->revalidated_flight->getFareSourceCode());
        $airBook->setUser($user);

        foreach ($request->input('passengers') as $passenger) {
            $t = AirTraveler::make()
                ->setName(firstName: $passenger['first_name'], middleName: $passenger->middle_name ?? '', lastName: $passenger['last_name'])
                ->setGender(TravellerGender::tryFrom($passenger['gender']))
                ->setBirthdate(Carbon::createFromFormat('Y-m-d', $passenger['birthdate']))
                ->setNationality($passenger['nationality'])
                ->setPassengerType(TravellerPassengerType::tryFrom($passenger['type']))
                ->setSeatPreference(TravellerSeatPreference::tryFrom('any'));
            if (
                ($request->revalidated_flight->isPassportMandatory() || $request->input('is_international'))
                && isset($passenger['passport'])
            ) {
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
        $booking = new AirBooking([
            'is_webfare' => $request->revalidated_flight->isWebfare(),
            'refund_type' => PartoRefundMethod::tryFrom($request->revalidated_flight->get('RefundMethod')),
            'type' => AirTripType::tryFrom($request->revalidated_flight->get('DirectionInd')),
            'origin_airport_code' => Arr::first($request->revalidated_flight->getFirstItinirary()['FlightSegments'])['DepartureAirportLocationCode'],
            'destination_airport_code' => Arr::last($request->revalidated_flight->getFirstItinirary()['FlightSegments'])['ArrivalAirportLocationCode'],
            'journey_begins_at' => $request->revalidated_flight->getFirstFlightSegmentTime()->format('Y-m-d H:i:s'),
            'journey_ends_at' => $request->revalidated_flight->getLastFlightSegmentTime()->format('Y-m-d H:i:s'),
            'airline_code' => $request->revalidated_flight->get('ValidatingAirlineCode'),
            'ref' => $request->input('ref')
        ]);
        $status = AirQueueStatus::Booked;

        if (! $booking->is_webfare) {
            $result = Parto::api()->air()->flightBook($airBook);
            abort_if(
                AirBookCategory::tryFrom($result->Category) != AirBookCategory::Booked,
                __('Couldn\'t book the flight. please try again!'),
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
                'amount' => $request->revalidated_flight->getTotalInRials(),
                'meta' => [
                    'breakdown' => FlightFareBreakdownResource::collection($request->revalidated_flight->get('AirItineraryPricingInfo')['PtcFareBreakdown'])
                ]
            ]));
            DB::commit();
            return [
                'ticket_time_limit' => $booking->valid_until->format('Y-m-d H:i:s'),
                'price_changed' => $result->PriceChange ?? false,
                'payment' => [
                    'amount' => $request->revalidated_flight->getTotalInRials(),
                    'currency' => 'IRR',
                    'breakdown' => $order->meta->breakdown,
                    'url' => route('orders.pay', ['order' => $order->id])
                ]
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            // Better to retry
            abort(500, __('Error while saving the order'. ' ' . __('Please try again') . '.'));
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
        $airBooking->load(['airline', 'origin_airport', 'destination_airport', 'order']);
        return response()->json(new AirBookingResource($airBooking));
    }

    public function show($airBooking)
    {
        $airBooking = AirBooking::withCount('passengers', 'flights')->findOrFail($airBooking);
        Gate::authorize('view', $airBooking);
        $airBooking->load(['airline', 'origin_airport', 'destination_airport']);
        $airBooking->load(['passengers', 'flights']);
        $airBooking->passengers->append('fullname')->makeHidden(['first_name', 'middle_name', 'last_name', 'title']);
        return response()->json(new UserAirBookingResource($airBooking));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AirBooking $airBooking, Request $request)
    {
        $request->validate([
            'ticket_numbers' => 'array',
            'ticket_numbers.*' => 'string',
        ]);
        // dd($airBooking->refund_type);
        if ($airBooking->refund_type == PartoRefundMethod::NonRefundable) {
            abort(403, __('Booking is non-refundable'));
        }
        try {
            switch ($airBooking->refund_type) {
                case PartoRefundMethod::Online:
                    $result = Parto::api()->air()->onlineRefund(
                        unique_id: $airBooking->parto_unique_id,
                        refundGroup: count($request->input('ticket_numbers', [])) < 1 ? RefundGroup::Pnr : RefundGroup::Eticket,
                        ticket_numbers: $request->input('ticket_numbers')
                    );
                    break;
                case PartoRefundMethod::Offline:
                    $result = Parto::api()->air()->offlineRefund(
                        unique_id: $airBooking->parto_unique_id,
                        ticket_numbers: $request->input('ticket_numbers')
                    );
                    break;
            }
            return [
                'okay' => $result->Success
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
