<?php

namespace App\Traits;

use App\Events\SearchForFlightProcessed;
use App\Models\Airline;
use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait FlightsSideJobs
{
    protected function takeCareOfSideEffects(Collection $collection_of_flights, Request $request)
    {
        [$unused, $origin] = explode(':', $request->input('origin'));
        [$unused2, $destination] = explode(':', $request->input('destination'));
        $this->saveAirlinesInSession($collection_of_flights);
        $this->saveAirportsInSession($collection_of_flights);
        SearchForFlightProcessed::dispatch(
            $collection_of_flights,
            "$origin:$destination",
            $request->input('date')
        );
    }
    protected function saveAirportsInSession(Collection $flights)
    {
        $departure_ariports = $flights->pluck('OriginDestinationOptions.*.FlightSegments.*.DepartureAirportLocationCode')
            ->flatten()
            ->filter()
            ->unique();
        $arrival_ariports = $flights->pluck('OriginDestinationOptions.*.FlightSegments.*.ArrivalAirportLocationCode')
            ->flatten()
            ->filter()
            ->unique();
        $all_airports = $departure_ariports->merge($arrival_ariports)->unique()->values();
        session()->flash('airports', Airport::whereIn('IATA_code', $all_airports)->select('IATA_code as code', 'name', 'name_fa')->get()->keyBy('code')->toArray());
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