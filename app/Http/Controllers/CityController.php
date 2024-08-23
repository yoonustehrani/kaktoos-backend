<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:10'
        ]);

        $citySearch = City::search();
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

        return $citySearch->simplePaginate($limit);
    }
}
