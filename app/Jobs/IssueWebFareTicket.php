<?php

namespace App\Jobs;

use App\Exceptions\PartoErrorException;
use App\Models\AirBooking;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Parto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
            $result = Parto::flightBook(
                flightBook: unserialize($this->airBooking->meta->webfare)
            );
            $this->airBooking->status = AirQueueStatus::tryFrom($result->Status);
            $this->airBooking->status_notes = AirQueueStatus::tryFrom($result->Status)->getDescription();
            $this->airBooking->save();
        } catch (PartoErrorException $error) {
            \Log::error($error->getErrorObject());
            $this->airBooking->status = AirQueueStatus::Exception;
            $this->airBooking->status_notes = $error->getMessage();
            $this->airBooking->save();
        }
    }
}
