<?php

namespace App\Parto\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Parto\Domains\FlightService flight()
 * @method static \App\Parto\Domains\Hotel\HotelServices hotel()
 * @method static \App\Parto\Client\PartoClient api()
 *
 * @see \App\Parto\PartoClient
 *
 * @mixin \Illuminate\Cache\Repository
 */
class Parto extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'parto';
    }
}
