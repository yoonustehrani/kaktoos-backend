<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:10'
        ]);

        $citySearch = City::search($request->query('q'));
        $filters = [
            'state_id' => 'state.id',
            'country_code' => 'state.country_code'
        ];
        foreach ($filters as $filter => $searchableKey) {
            if ($request->has($filter)) {
                $citySearch->where($searchableKey, $request->query($filter));
            }
        }
        $limit = $request->query('limit') ?? 10;

        $cities = $citySearch->take($limit)->simplePaginate($limit);
        $cities->load('state.country');
        return response()->json($cities);
    }
}
