<?php

namespace App\Jobs;

use App\Exceptions\PartoErrorException;
use App\Models\AirBooking;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Facades\Parto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueNonWebFareTicket implements ShouldQueue
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
            $result = Parto::api()->air()->orderTicket(unique_id: $this->airBooking->parto_unique_id);
            $this->airBooking->status = AirQueueStatus::tryFrom($result->Status);
            $this->airBooking->save();
        } catch (PartoErrorException $error) {
            $this->airBooking->status = AirQueueStatus::Exception;
            $this->airBooking->status_notes = $error->getMessage();
            $this->airBooking->meta = array_merge($this->airBooking->meta, [
                'error' => [
                    'id' => $error->id,
                    'message' => $error->getMessage()
                ]
            ]);
            $this->airBooking->save();
        }
        
    }
}
