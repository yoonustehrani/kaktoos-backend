<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelSearchRequest;
use App\Models\Hotel;
use App\Parto\Domains\Hotel\Builder\HotelSearchQueryBuilder;
use App\Parto\Facades\Parto;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:10',
            'city_id' => 'integer|min:1',
            'state_id' => 'integer|min:1',
            'rating' => 'integer|min:1|max:5',
            'country_code' => 'string|size:2'
        ]);
        
        $hotelSearch = Hotel::search($request->query('q'));
        $filters = [
            'city_id' => 'city.id',
            'rating' => 'rating',
            'state_id' => 'city.state.id',
            'country_code' => 'city.state.country.code'
        ];
        foreach ($filters as $filter => $searchableKey) {
            if ($request->has($filter)) {
                $hotelSearch->where($searchableKey, $request->query($filter));
            }
        }
        $limit = $request->query('limit') ?? 10;
        // $hotelSearch->orderBy('rating', 'desc');
        $hotels = $hotelSearch->take($limit)->simplePaginate($limit);
        $hotels->load('city.state.country');

        return response()->json($hotels);
    }

    public function showCity(int $cityId, HotelSearchRequest $request)
    {
        return Parto::api()->searchHotels( 
            $this->getPartoHotelQuery($request, Parto::hotel()->hotelSearch()->searchByCityId($cityId))
        );
    }

    public function show(int $hotelId, HotelSearchRequest $request)
    {
        return Parto::api()->searchHotels( 
            $this->getPartoHotelQuery($request, Parto::hotel()->hotelSearch()->searchByHotelId($hotelId))
        );
    }

    protected function getPartoHotelQuery(Request $request, HotelSearchQueryBuilder $builder): array
    {
        $builder->setDates($request->input('start_date'), $request->input('end_date'));
        for ($i=0; $i < count($request->input('rooms')); $i++) { 
            $builder->addRoom(
                adultCount: $request->input("rooms.$i.adults"),
                childCount: $request->input("rooms.$i.children", 0),
                childAges: $request->input("rooms.$i.children_age", [])
            );
        }
        return $builder->get();
    }
}

