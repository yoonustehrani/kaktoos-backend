<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightPricesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class FlightPriceController extends Controller
{
    public function show(FlightPricesRequest $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $route = $request->query('route');
        // Filter prices between the specified dates
        $filteredPrices = Cache::remember(
            key: "calendar:$startDate:$endDate:$route",
            ttl: 60, // seconds
            callback: function() use($startDate, $endDate, $route) {
                $prices = Redis::hgetall($route);
                $filteredPrices = [];
                foreach ($prices as $date => $price) {
                    if ($date >= $startDate && $date <= $endDate) {
                        $filteredPrices[$date] = (float) $price;
                    }
                }
                return $filteredPrices;
            }
        );
        return response()->json($filteredPrices);
    }
}
