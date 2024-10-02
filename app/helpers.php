<?php

use App\Attributes\DisplayFa;
use App\Contracts\CustomEnum;
use App\Models\AirBooking;
use App\Models\Order;
use App\Models\Parto\Hotel\HotelBooking;
use App\Parto\Domains\Flight\PricedItinerary;
use App\Parto\Facades\Parto;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

if (! function_exists('get_auth_user')) {
    /**
     * @return \App\Models\User
     */
    function get_auth_user(): \App\Models\User
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}

if (! function_exists('get_carbon_datetime')) {
    function get_carbon_datetime(string $parto_datetime): Carbon
    {
        return Carbon::createFromFormat('Y-m-d\TH:i:s', $parto_datetime);
    }
}

if (! function_exists('get_order_final_url')) {
    function get_order_final_url(Order $order)
    {
        $url = str_replace('api.', '', config('app.url'));
        switch ($order->purchasable_type) {
            case AirBooking::class:
                $url .= '/flight/final';
                break;
            case HotelBooking::class:
                $url .= '/hotel/final';
                break;
            default:
                throw new Exception('Purchasable type not supported!');
        }
        if (method_exists($order->purchasable, 'getUri')) {
            $target = urlencode($order->purchasable->getUri());
            $url .= '?url=' . $target;
        }
        return $url;
    }
}

if (! function_exists('get_flight_total_price')) {
    function get_flight_total_price(AirBooking $booking)
    {
        if (is_null($booking->parto_unique_id)) {
            $price = (new PricedItinerary(Parto::api()->air()->revalidate($booking->ref)->PricedItinerary))->getTotalInRials();
        } else {
            $price = Arr::get(
                Parto::api()->air()->getBookingDetails($booking->parto_unique_id)->TravelItinerary,
                'ItineraryInfo.ItineraryPricing.TotalFare'
            );
        }
        return intval($price);
    }
}
