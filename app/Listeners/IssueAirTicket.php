<?php

namespace App\Listeners;

use App\Events\AirBookingOrderPaid;
use App\Jobs\IssueNonWebFareTicket;
use App\Jobs\IssueWebFareTicket;
use App\Parto\Parto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IssueAirTicket
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
            IssueWebFareTicket::dispatchSync($booking);
        } else {
            IssueNonWebFareTicket::dispatchSync($booking);
        }
    }
}
