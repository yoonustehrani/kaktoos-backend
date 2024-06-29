<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightSearchRequest;
use App\Http\Resources\FlightSearchCollection;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\FlightLocationType;
use App\Parto\Domains\Flight\FlightOriginDestination;
use App\Traits\FlightsSideJobs;
use App\Traits\PaginatesCollections;
use Illuminate\Support\Carbon;

class FlightApiController extends Controller
{
    use PaginatesCollections, FlightsSideJobs;

    public function searchOneWay(FlightSearchRequest $request)
    {
        [$originLocationType, $origin] = explode(':', $request->input('origin'));
        [$destinationLocationType, $destination] = explode(':', $request->input('destination'));
        /**
         * @var \App\Parto\PartoClient
         */
        $parto = app('parto');
        $flight_search = $parto->flight()->flightSearch()
            ->setCount(
                adult: $request->input('passengers.adults'),
                child: $request->input('passengers.children', 0),
                infant: $request->input('passengers.infants', 0)
                
            )
            ->setCabinType(FlightCabinType::tryFrom($request->input('cabin_type', null)))
            ->oneWay(new FlightOriginDestination(
                originCode: $origin,
                originLocationType: FlightLocationType::tryFrom($originLocationType),
                destinationCode: $destination,
                destinationLocationType: FlightLocationType::tryFrom($destinationLocationType),
                departureDateTime: Carbon::createFromFormat('Y-m-d', $request->input('date'))
            ));
        
        $cache_key = implode(".", [$request->input('origin') , $request->input('destination') , $request->input('date')]);
        $flights = cache()->remember(md5($cache_key), config('services.parto.timing.flights_cache'), function () use($parto, $flight_search) {
            return $parto->searchFlight($flight_search)?->PricedItineraries ?? [];
        });
        if (count($flights) > 0) {
            $this->takeCareOfSideEffects(collect($flights), $request);
        }
        return response()->json(
            new FlightSearchCollection($this->paginate($flights, 50))
        );
    }
}
