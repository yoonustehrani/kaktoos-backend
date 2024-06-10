<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InternationalAirportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'string|min:2',
            'limit' => 'nullable|numeric|min:1|max:10'
        ]);
        $search_query = $request->query('q');
        $airports = Airport::onlyInternational();
        if (preg_match('/^[A-Z]{2,4}$/', $search_query)) {
            $airports->where('IATA_code', 'like', "$search_query%");
        } else if (preg_match('/[A-Za-z]+/', $search_query)) {
            $airports->where('name', 'like', "%$search_query%");
            $airports->orWhere('city_name', 'like', "%$search_query%");
        } else if (preg_match('/[^a-zA-Z0-9\_\@\!\/\$\#\^\&\*\(\)\-\+]{1,}/', $search_query)) {
            $airports->where('name_fa', 'like', "%$search_query%");
            $airports->orWhere('city_name_fa', 'like', "%$search_query%");
        }
        $limit = $request->query('limit') ?? '5';
        $airports->limit($limit);
        return $airports->get()->groupBy('city_name_fa');
    }
}
