<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderCompleted;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MarkorderAsCompleted
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
    public function handle(OrderCompleted $event): void
    {
        $event->order->update([
            'status' => OrderStatus::COMPLETED
        ]);
    }
}
