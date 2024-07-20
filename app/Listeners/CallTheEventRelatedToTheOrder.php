<?php

namespace App\Listeners;

use App\Events\AirBookingOrderPaid;
use App\Events\OrderPaid;
use App\Models\AirBooking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CallTheEventRelatedToTheOrder
{
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
    public function handle(OrderPaid $event): void
    {
        switch ($event->order->purchasable_type) {
            case AirBooking::class:
                AirBookingOrderPaid::dispatch($event->order);
                break;
        }
    }
}
