<?php

namespace App\Events;

use App\Parto\PartoFlight;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SearchForFlightProcessed
{
    use Dispatchable, SerializesModels;
    public PartoFlight $cheapestFlight;
    /**
     * Create a new event instance.
     */
    public function __construct(Collection $flights, string $route, string $date)
    {
        $pricesPerAdult = $flights->pluck('AirItineraryPricingInfo.PtcFareBreakdown.*')
        ->map(function($passengerPricing) {
            return array_filter($passengerPricing, fn($p) => $p['PassengerTypeQuantity']['PassengerType'] === 1)[0]['PassengerFare']['TotalFare'];
        })
        ->flatten()
        ->filter()
        ->unique()
        ->sort()
        ->values();
        $this->cheapestFlight = new PartoFlight($pricesPerAdult->first(), $route, $date);
    }
}
