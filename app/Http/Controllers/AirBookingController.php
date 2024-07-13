<?php

namespace App\Http\Controllers;

use App\Exceptions\PartoErrorException;
use App\Http\Requests\FlightBookingRequest;
use App\Models\AirBooking;
use App\Models\User;
use App\Parto\Domains\Flight\Enums\TravellerPassengerType;
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
        auth()->login(User::first());
        $amount = $request->revalidated_flight->getTotalInRials();
        /**
         * @var \App\Payment\PaymentGateway
         */
        $purchase = app()->make(PaymentGateway::getGatewayClassname('jibit'));
        $purchase->gateway->setRequestItem('description', 'پرداخت برای تست');
        $purchase->init(amount: $amount);
        $purchase->requestPurchase();
        return [
            'amount' => $amount,
            'gateway' => 'jibit',
            'redirect_url' => $purchase->getRedirectUrl()
        ];
        // if () {
        //     return $purchase->redirect();
        // }
        // if ($revalidated_flight['IsPassportMandatory']) {
        //     $request->validate([
        //         'passengers.*.passport' => ['required', 'array'],
        //         'passengers.*.passport.country' => 'required|string|size:2|alpha|regex:/[A-Z]{2}/',
        //         'passengers.*.passport.passport_number' => 'required|string|alpha_num',
        //         'passengers.*.passport.expiry_date' => 'required|date|date_format:Y-m-d',
                
        //     ]);
        // }
        
        // try {
            
        // } catch (PartoErrorException $error) {
        //     switch ($error->id) {
        //         case '':
        //             abort(404, $error->);       
        //             break;
        //         default:
        //             throw $error;
        //             break;
        //     }
        // }
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
