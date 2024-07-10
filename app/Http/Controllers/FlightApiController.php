<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightSearchRequest;
use App\Http\Resources\FlightFareRuleResource;
use App\Http\Resources\FlightSearchCollection;
use App\Models\Airline;
use App\Models\Airport;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\FlightLocationType;
use App\Parto\Domains\Flight\FlightSearch\FlightOriginDestination;
use App\Parto\Domains\Flight\FlightSearch\FlightSearch;
use App\Parto\Parto;
use App\Traits\FlightsSideJobs;
use App\Traits\PaginatesCollections;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FlightApiController extends Controller
{
    use PaginatesCollections, FlightsSideJobs;

    public function getFareRules(Request $request)
    {
        $request->validate([
            'ref' => 'required|string|min:10',
        ]);
        $rules = collect(Parto::getFareRule($request->input('ref'))?->FareRules ?? []);
        $airlines = Airline::whereIn(
            'code', 
            $rules->pluck('Airline')->flatten()->filter()->unique()->values()
        )->get()->keyBy('code');
        $airports = Airport::select('IATA_code as code', 'name', 'name_fa')->whereIn(
            'IATA_code',
            $rules->pluck('CityPair')->flatten()->filter()->map(fn($item) => explode('-', $item))->flatten()->unique()->values()
        )->get()->keyBy('code');
        return response()->json(
            FlightFareRuleResource::collection($rules)
        );
    }

    public function getBaggageRules(Request $request)
    {
        $request->validate([
            'ref' => 'required|string|min:10',
        ]);

        return response()->json(
            Parto::getBaggageRule($request->input('ref'))
        );
    }

    public function search(string $method, FlightSearchRequest $request)
    {
        abort_if(! in_array($method, ['one-way', 'roundtrip']), 404, "Search method not found");
        [$originLocationType, $origin] = explode(':', $request->input('origin'));
        [$destinationLocationType, $destination] = explode(':', $request->input('destination'));

        $parto = Parto::flight();
        $flight_search = $parto->flightSearch()
            ->setCount(
                adult: $request->input('passengers.adults'),
                child: $request->input('passengers.children', 0),
                infant: $request->input('passengers.infants', 0)
                
            )
            ->setCabinType(FlightCabinType::tryFrom($request->input('cabin_type', null)));
        $cache_key = null;
        switch ($method) {
            case 'one-way':
                $flight_search->oneWay(new FlightOriginDestination(
                    originCode: $origin,
                    originLocationType: FlightLocationType::tryFrom($originLocationType),
                    destinationCode: $destination,
                    destinationLocationType: FlightLocationType::tryFrom($destinationLocationType),
                    departureDateTime: Carbon::createFromFormat('Y-m-d', $request->input('date'))
                ));
                $cache_key = implode(".", [$request->input('origin') , $request->input('destination') , $request->input('date')]);
                break;
            case 'roundtrip':
                $flight_search->roundtrip(
                    first: new FlightOriginDestination(
                        originCode: $origin,
                        originLocationType: FlightLocationType::tryFrom($originLocationType),
                        destinationCode: $destination,
                        destinationLocationType: FlightLocationType::tryFrom($destinationLocationType),
                        departureDateTime: Carbon::createFromFormat('Y-m-d', $request->input('date'))
                    ),
                    second: new FlightOriginDestination(
                        originCode: $destination,
                        originLocationType: FlightLocationType::tryFrom($destinationLocationType),
                        destinationCode: $origin,
                        destinationLocationType: FlightLocationType::tryFrom($originLocationType),
                        departureDateTime: Carbon::createFromFormat('Y-m-d', $request->input('return_date'))
                    )
                );
                $cache_key = implode(".", [$request->input('origin') , $request->input('destination') , $request->input('date'), $request->input('return_date')]);
                break;
        }
        return $this->returnFlights(
            flightSearch: $flight_search,
            request: $request,
            cache_key: $cache_key
        );
    }

    protected function returnFlights(FlightSearch $flightSearch, Request $request, string $cache_key)
    {
        $flights = cache()->remember(md5($cache_key), config('services.parto.timing.flights_cache'), function () use($flightSearch) {
            return app('parto')->searchFlight($flightSearch)?->PricedItineraries ?? [];
        });
        if (count($flights) > 0) {
            $this->takeCareOfSideEffects(collect($flights), $request);
        }
        return response()->json(
            new FlightSearchCollection($this->paginate($flights, 50)) 
        );
    }
}
