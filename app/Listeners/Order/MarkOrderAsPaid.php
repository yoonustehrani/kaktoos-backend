<?php

namespace App\Listeners\Order;

use App\Events\OrderPaid;
use Illuminate\Support\Facades\DB;

class MarkOrderAsPaid
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
        DB::transaction(function() use($event) {
            $event->order->paid_at = now();
            $event->order->save();
            $event->order->user?->decreaseCredit($event->order->amount);
        });
    }
}
