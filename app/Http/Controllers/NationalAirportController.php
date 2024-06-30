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
            'q' => 'nullable|string|min:2',
            'limit' => 'nullable|numeric|min:1|max:10'
        ]);

        $cities = \DB::table('airports');
        $cities->where('country_code', 'IR')->whereNotNull('city_name_fa')->where('IATA_code', '!=', 'IKA');
        $limit = $request->query('limit') ?? '5';
        $cities->limit($limit);
        $cities->orderBy('rating', 'desc');

        if ($request->missing('q')) {
            return response()->json(
                $cities->get()
            );
        }

        $search_query = $request->query('q');
        if (preg_match('/^[A-Z]{2,3}$/', $search_query)) {
            $cities->where(function($query) use($search_query) {
                $query->where('IATA_code', 'like', "$search_query%");
            });
        } elseif (preg_match('/[A-Za-z]+/', $search_query)) {
            $cities->where(function($subquery) use($search_query) {
                if (preg_match('/^[A-Za-z]{2,3}$/', $search_query)) {
                    $subquery->where('IATA_code', 'like', "$search_query%");
                    $subquery->orWhere('name', 'like', "%$search_query%");
                    $subquery->orWhere('city_name', 'like', "%$search_query%");
                } else {
                    $subquery->where('name', 'like', "%$search_query%");
                    $subquery->orWhere('city_name', 'like', "%$search_query%");
                } 
            });
        } elseif(preg_match('/[^a-zA-Z0-9\_\@\!\/\$\#\^\&\*\(\)\-\+]{1,}/', $search_query)) {
            $cities->where(function(Builder $query) use($search_query) {
                $query->where('name_fa', 'like', "%$search_query%");
                $query->orWhere('city_name_fa', 'like', "%$search_query%");
            });
        }
            
        
        $cities->groupBy('IATA_code')->select('city_name_fa', 'IATA_code');
        
        return response()->json($cities->get());
    }
    /**
     * Display the specified resource.
     */
    public function show(Airport $airport)
    {
        //
    }
}
