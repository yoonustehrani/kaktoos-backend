<?php

namespace App\Jobs;

use App\Events\OrderCompleted;
use App\Exceptions\PartoErrorException;
use App\Models\AirBooking;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Facades\Parto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class IssueWebFareTicket implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public AirBooking $airBooking)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $result = Parto::api()->air()->flightBook(
                flightBook: unserialize($this->airBooking->meta->webfare)
            );
            $this->airBooking->parto_unique_id = $result->UniqueId;
            $this->airBooking->status = AirQueueStatus::tryFrom($result->Status);
            $this->airBooking->meta->webfare = null;
            $this->airBooking->save();
            OrderCompleted::dispatch($this->airBooking->order);
        } catch (PartoErrorException $error) {
            Log::error(json_encode($error->getErrorObject(), JSON_PRETTY_PRINT));
            $this->airBooking->status = AirQueueStatus::Exception;
            $this->airBooking->status_notes = $error->getMessage();
            $this->airBooking->save();
            RefundOrder::dispatch($this->airBooking->order);
        }
    }
}
