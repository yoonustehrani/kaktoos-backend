<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\AirBooking;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RefundOrder implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->order->status != OrderStatus::REFUNDED) {
            try {
                DB::beginTransaction();
                $this->order->update([
                    'status' => OrderStatus::REFUNDED
                ]);
                $this->order->user->increaseCredit($this->order->amount);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        }
    }
}
