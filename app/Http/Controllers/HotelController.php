<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelSearchRequest;
use App\Http\Resources\HotelImageResource;
use App\Http\Resources\PartoHotelOfferResource;
use App\Models\Hotel;
use App\Parto\Domains\Hotel\Builder\HotelSearchQueryBuilder;
use App\Parto\Facades\Parto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $offers = Parto::api()->searchHotels( 
            $this->getPartoHotelQuery($request, Parto::hotel()->hotelSearch()->searchByHotelId($hotelId))
        )->PricedItineraries ?? null;
        if (! $offers) {
            abort(500, 'No Hotel Offers Found');
        }
        return response()->json([
            'hotel' => Hotel::with(['accommodation', 'city.state.country'])->find($hotelId),
            'offers' => PartoHotelOfferResource::collection($offers)
        ]);
    }

    public function hotelImages(int $hotelId)
    {
        $hotelImages = Cache::remember("hotel-$hotelId-images", 60 * 60 * 24, function() use($hotelId) {
            return Parto::api()->requestHotelImages($hotelId) ?? null;
        });
        if (! $hotelImages) {
            abort(500, 'No hotel image found');
        }
        $links = HotelImageResource::collection($hotelImages->Links);
        return response()->json(
            collect($links->toArray(request()))->groupBy('group')
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

