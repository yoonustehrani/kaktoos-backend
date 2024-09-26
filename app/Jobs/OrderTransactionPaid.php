<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Events\OrderPaid;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class OrderTransactionPaid implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->transaction->order;
        if ($order->status == OrderStatus::PENDING) {
            DB::transaction(function() use($order) {
                $order->user?->decreaseCredit($this->transaction->amount);
                $order->increment('amount_paid', $this->transaction->amount);
                if ($order->amount == $order->amount_paid) {
                    $order->update([
                        'status' => OrderStatus::PROCESSING,
                        'paid_at' => now()
                    ]);
                    OrderPaid::dispatch($order);
                } else {
                    $order->update([
                        'status' => OrderStatus::PENDING
                    ]);
                }
            });
        }
    }
}
