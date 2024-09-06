<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelBookingRequest;
use App\Http\Resources\HotelBookingResource;
use App\Models\Order;
use App\Models\Parto\Hotel\HotelBooking;
use App\Parto\Enums\HotelQueueStatus;
use App\Parto\Facades\Parto;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    public function show(HotelBooking $booking, Request $request)
    {
        return Parto::api()->hotel()->getBookingData($booking->parto_unique_id, $request->user()->id);
    }

    public function store(string $ref, HotelBookingRequest $request)
    {
        $offer = Parto::api()->hotel()->checkOffer($ref)->PricedItinerary;
        $booking = new HotelBooking([
            'hotel_id' => $offer['HotelId'],
            'status' => HotelQueueStatus::Created
        ]);
        $booking->meta = [
            'ref' => $offer['FareSourceCode'],
            'rooms' => $request->input('rooms')
        ];
        $booking = $request->user()->hotelBookings()->save($booking);
        $order = new Order([
            'user_id' => $request->user()->id,
            'title' => 'رزور هتل',
            'amount' => $offer['NetRate']
        ]);
        $booking->order()->save($order);
        return [
            // 'time_limit' => $booking->payment_valid_until->format('Y-m-d H:i:s'),
            'payment' => [
                'amount' => $order->amount,
                'currency' => $offer['Currency'],
                'url' => route('orders.pay', ['order' => $order->id])
            ]
        ];
        return response()->json(
            new HotelBookingResource($hotelBooking)
        );
    }
}
