<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\AirBookingOrderPaid;
use App\Exceptions\PartoErrorException;
use App\Jobs\CancellAirOrder;
use App\Jobs\IssueNonWebFareTicket;
use App\Jobs\IssueWebFareTicket;
use App\Jobs\RefundOrder;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Domains\Flight\PricedItinerary;
use App\Parto\Facades\Parto;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class IssueAirTicket implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
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
        try {
            /**
             * @var \App\Models\AirBooking
             */
            $booking = $event->order->purchasable;
            if (get_flight_total_price($booking) != $event->order->amount) {
                if (! $booking->is_webfare) {
                    CancellAirOrder::dispatch($booking);
                }
                throw new Exception('Price changed', 998);
            }
            if ($booking->is_webfare) {                
                IssueWebFareTicket::dispatch($booking);
            } else {
                IssueNonWebFareTicket::dispatch($booking);
            }
        } catch(PartoErrorException $error) {
            $booking->update([
                'status' => AirQueueStatus::Exception,
                'status_notes' => $error->getMessage()
            ]);
            $event->order->update([
                'status' => OrderStatus::CANCELLED
            ]);
            RefundOrder::dispatch($event->order);
        } catch (\Throwable $th) {
            if ($th->getCode() === 998) {
                $booking->update([
                    'status' => AirQueueStatus::Exception,
                    'status_notes' => __('Price changed')
                ]);
            } else {
                $event->order->update([
                    'status' => OrderStatus::CANCELLED
                ]);
                RefundOrder::dispatch($event->order);
            }
            throw $th;
        }
    }
}
