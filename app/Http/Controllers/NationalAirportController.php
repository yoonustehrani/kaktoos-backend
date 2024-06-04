<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class NationalAirportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'string|min:2'
        ]);
        $search_query = $request->query('q');
        $cities = \DB::table('airports');
        $cities->where('country_code', 'IR')->whereNotNull('city_name_fa');
        if (preg_match('/^[A-Z]{2,4}$/', $search_query)) {
            $cities->where('IATA_code', 'like', "$search_query%");
        } else if (preg_match('/[A-Za-z]+/', $search_query)) {
            $cities->where(function(Builder $query) use($search_query) {
                $query->where('name', 'like', "%$search_query%");
                $query->orWhere('city_name', 'like', "%$search_query%");
            });
        } else if (preg_match('/[^a-zA-Z0-9\_\@\!\/\$\#\^\&\*\(\)\-\+]{1,}/', $search_query)) {
            $cities->where(function(Builder $query) use($search_query) {
                $query->where('name_fa', 'like', "%$search_query%");
                $query->orWhere('city_name_fa', 'like', "%$search_query%");
            });
        }
        $cities->groupBy('IATA_code')->where('IATA_code', '!=', 'IKA')->select('city_name_fa', 'IATA_code');
        return $cities->get();
        //
    }
    /**
     * Display the specified resource.
     */
    public function show(Airport $airport)
    {
        //
    }
}
