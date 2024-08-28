<?php

namespace App\Http\Controllers\Parto;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelImageResource;
use App\Parto\Facades\Parto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HotelImageController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'ids' => 'required|regex:/^(\d,?)+$/'
        ]);
        $key = $request->query('ids');
        $ids = array_map('intval', explode(',', $request->query('ids')));
        $hotelImages = Cache::remember("hotel-$key-images", 60 * 60 * 24, function() use($ids) {
            return Parto::api()->requestHotelImagesBulk($ids) ?? null;
        });
        if (! $hotelImages) {
            abort(500, 'No hotel image found');
        }
        return response()->json(
            collect($hotelImages->HotelImages)
                ->groupBy('HotelId')
                ->map(fn($item) => $item[0]['Links'])
                ->map(fn($item) => [
                    new HotelImageResource($item[0])
                ])
        );
    }

    public function show(int $hotelId)
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
}
