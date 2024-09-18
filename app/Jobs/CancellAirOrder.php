<?php

namespace App\Jobs;

use App\Models\AirBooking;
use App\Parto\Domains\Flight\Enums\AirBook\AirQueueStatus;
use App\Parto\Facades\Parto;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancellAirOrder implements ShouldQueue
{
    use Queueable;

    // public $delay = 60;

    // public $tries = 3;

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
        Parto::api()->air()->cancel($this->airBooking->parto_unique_id);
        $this->airBooking->update([
            'status' => AirQueueStatus::Cancelled,
            'status_notes' => ''
        ]);
    }
}
