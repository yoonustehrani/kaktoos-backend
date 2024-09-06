<?php

namespace App\Listeners;

use App\Events\AirBookingOrderPaid;
use App\Jobs\IssueNonWebFareTicket;
use App\Jobs\IssueWebFareTicket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IssueAirTicket implements ShouldQueue
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
    public function handle(AirBookingOrderPaid $event): void
    {
        $booking = $event->order->purchasable;
        if ($booking->is_webfare) {
            IssueWebFareTicket::dispatch($booking);
        } else {
            IssueNonWebFareTicket::dispatch($booking);
        }
    }
}
