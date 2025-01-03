<?php

namespace App\Listeners\Order;

use App\Events\AirBookingOrderPaid;
use App\Events\OrderPaid;
use App\Events\Parto\HotelBookingOrderPaid;
use App\Models\AirBooking;
use App\Models\Parto\Hotel\HotelBooking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CallTheEventRelatedToTheOrder implements ShouldQueue
{
    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 5;

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
            case HotelBooking::class:
                HotelBookingOrderPaid::dispatch($event->order);
                break;
        }
    }
}
