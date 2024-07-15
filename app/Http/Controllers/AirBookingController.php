<?php

namespace App\Http\Controllers;

use App\Exceptions\PartoErrorException;
use App\Http\Requests\FlightBookingRequest;
use App\Models\AirBooking;
use App\Models\User;
use App\Parto\Domains\Flight\Enums\AirBook\AirBookCategory;
use App\Parto\Domains\Flight\Enums\TravellerGender;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
use App\Parto\Domains\Flight\Enums\TravellerSeatPreference;
use App\Parto\Domains\Flight\FlightBook\AirTraveler;
use App\Parto\Parto;
use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

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
        
        $result = Parto::flightBook($airBook);
        // TODO: Unique id should be saved in databse
        // TODO: db record id should be passed to Payment Gateway
        $booking = new AirBooking();
        // TODO: this should be dynamic
        $booking->is_webfare = false;
        $booking->parto_unique_id = $result->UniqueId;
        // TODO: this should be based on enum
        $booking->status = AirBookCategory::tryFrom($result->Category);
        $booking->status_notes = '';
        try {
            $booking->valid_until = Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $result->TktTimeLimit);
        } catch (\Throwable $th) {
            $booking->valid_until = Carbon::createFromFormat('Y-m-d\TH:i:s', $result->TktTimeLimit);
        }
        $booking = $user->airBookings()->save($booking);

        $amount = $request->revalidated_flight->getTotalInRials();
        /**
         * @var \App\Payment\PaymentGateway
         */
        $purchase = app()->make(PaymentGateway::getGatewayClassname('jibit'));
        $purchase->gateway->setRequestItem('description', 'پرداخت برای تست');
        $purchase->init(amount: $amount, ref: $booking->id);
        $purchase->requestPurchase();
        return [
            'ticket_time_limit' => $booking->valid_until->format('Y-m-d H:i:s'),
            'price_changed' => $result->PriceChange,
            'amount' => $amount,
            'gateway' => 'jibit',
            'redirect_url' => $purchase->getRedirectUrl()
        ];
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
