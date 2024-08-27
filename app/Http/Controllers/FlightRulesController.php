<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlightFareRuleResource;
use App\Http\Resources\PartoWithMetaCollection;
use App\Http\Resources\PartoBaggageRuleResource;
use App\Models\Airline;
use App\Models\Airport;
use App\Parto\Facades\Parto;
use Illuminate\Http\Request;

class FlightRulesController extends Controller
{
    public function fare(Request $request)
    {
        $request->validate([
            'ref' => 'required|string|min:10',
        ]);
        $rules = collect(Parto::api()->getFareRule($request->input('ref'))?->FareRules ?? []);
        $airlines = Airline::whereIn(
            'code', 
            $rules->pluck('Airline')->flatten()->filter()->unique()->values()
        )->get()->keyBy('code');
        $airports = Airport::select('IATA_code as code', 'name', 'name_fa')->whereIn(
            'IATA_code',
            $rules->pluck('CityPair')->flatten()->filter()->map(fn($item) => explode('-', $item))->flatten()->unique()->values()
        )->get()->keyBy('code');
        return response()->json(
            (new PartoWithMetaCollection(
                FlightFareRuleResource::collection($rules)
            ))->withMeta(compact('airlines', 'airports'))
        );
    }

    public function baggage(Request $request)
    {
        $request->validate([
            'ref' => 'required|string|min:10',
        ]);
        // TODO => ->Services
        $rules = Parto::api()->getBaggageRule($request->input('ref'))?->BaggageInfoes;
        if ($rules) {
            $rules = collect($rules);
            $airports = $rules->pluck('Arrival')->flatten()->filter();
            $airports = $rules->pluck('Departure')->flatten()->filter()->merge($airports)->unique()->values();
            $airports = Airport::select('IATA_code as code', 'name', 'name_fa')->whereIn(
                'IATA_code',
                $airports
            )->get()->keyBy('code');
            return response()->json(
                (new PartoWithMetaCollection(
                    PartoBaggageRuleResource::collection($rules)
                ))->withMeta(compact('airports'))
            );
        }
    }
}
