<?php

namespace App\Traits;

use App\Events\SearchForFlightProcessed;
use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait FlightsSideJobs
{
    protected function takeCareOfSideEffects(Collection $collection_of_flights, Request $request)
    {
        [$unused, $origin] = explode(':', $request->input('origin'));
        [$unused2, $destination] = explode(':', $request->input('destination'));
        $this->saveAirlinesInSession($collection_of_flights);
        
        SearchForFlightProcessed::dispatch(
            $collection_of_flights,
            "$origin:$destination",
            $request->input('date')
        );
    }
    protected function saveAirlinesInSession(Collection $flights)
    {
        $marketing_airlines = $flights->pluck('OriginDestinationOptions.*.FlightSegments.*.MarketingAirlineCode')
            ->flatten()
            ->filter()
            ->unique();
        $operating_airlines = $flights->pluck('OriginDestinationOptions.*.FlightSegments.*.OperatingAirline.Code')
            ->flatten()
            ->filter()
            ->unique()
        ->all();
        $all_airlines = $marketing_airlines->merge($operating_airlines)->unique()->values();
        session()->flash('airlines', Airline::whereIn('code', $all_airlines)->get()->keyBy('code')->toArray());
    }
}