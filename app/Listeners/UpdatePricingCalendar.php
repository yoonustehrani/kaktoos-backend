<?php

namespace App\Listeners;

use App\Events\SearchForFlightProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;

class UpdatePricingCalendar 
// implements ShouldQueue
{
    // use InteractsWithQueue;
    public $delay = 3;
    public $tries = 2;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SearchForFlightProcessed $event): void
    {
        $flight = $event->cheapestFlight;
        $redis_hash_key = $flight->route;
        Redis::hset($redis_hash_key, $flight->date, $flight->pricePerAdult);
    }
}
