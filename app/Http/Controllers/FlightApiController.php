<?php

namespace App\Http\Controllers;

use App\Events\SearchForFlightProcessed;
use App\Http\Requests\FlightSearchRequest;
use App\Http\Resources\FlightSearchCollection;
use App\Models\Airline;
use App\Parto\Domains\Flight\Enums\FlightCabinType;
use App\Parto\Domains\Flight\Enums\FlightLocationType;
use App\Parto\Domains\Flight\FlightOriginDestination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FlightApiController extends Controller
{
    public function searchOneWay(FlightSearchRequest $request)
    {
        /**
         * @var \App\Parto\PartoClient
         */
        $parto = app('parto');
        [$originLocationType, $origin] = explode(':', $request->input('origin'));
        [$destinationLocationType, $destination] = explode(':', $request->input('destination'));
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


        // return $parto->searchFlight($flight_search);
        $cache_key = implode(".", [$request->input('origin') , $request->input('destination') , $request->input('date')]) . "1";
        $flights = cache()->remember(md5($cache_key), 60 * 60, function () use($parto, $flight_search) {
            return $parto->searchFlight($flight_search)?->PricedItineraries ?? [];
        });
        $collection_of_flights = collect($flights);

        $this->saveAirlinesInSession($collection_of_flights);
        
        SearchForFlightProcessed::dispatch(
            $collection_of_flights,
            "$origin:$destination",
            $request->input('date')
        );

        return response()->json(
            new FlightSearchCollection($this->paginate($flights, 50))
        );
    }


    private function saveAirlinesInSession(Collection $flights)
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
    /**
     * Paginate an array of items.
     *
     * @return LengthAwarePaginator         The paginated items.
     */
    private function paginate(array $items, int $perPage = 5, ?int $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);
        $items = collect($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
