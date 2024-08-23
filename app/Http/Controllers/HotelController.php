<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelSearchRequest;
use App\Models\Hotel;
use App\Parto\Parto;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:10'
        ]);
        
        $hotelSearch = Hotel::search();
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
        return $hotelSearch->simplePaginate($limit);
    }

    public function showCity(int $cityId, HotelSearchRequest $request)
    {
        $service = Parto::hotel()->hotelSearch();

        return Parto::searchHotels(
            $service->searchByCityId($cityId)
                ->setDates($request->input('start_date'), $request->input('end_date'))
                ->setPeople(
                    adultCount: $request->input('residents.adults'),
                    childCount: $request->input('residents.children', 0),
                    childAges: $request->input('residents.children_age', [])
                )
                ->get()
        );
    }

    public function show(int $hotelId, HotelSearchRequest $request)
    {
        $service = Parto::hotel()->hotelSearch();

        return Parto::searchHotels(
            $service->searchByHotelId($hotelId)
                ->setDates($request->input('start_date'), $request->input('end_date'))
                ->setPeople(
                    adultCount: $request->input('residents.adults'),
                    childCount: $request->input('residents.children', 0),
                    childAges: $request->input('residents.children_age', [])
                )
                ->get()
        );
    }
}
