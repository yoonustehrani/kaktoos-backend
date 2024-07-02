<?php

namespace App\Parto;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Parto\Domains\FlightService flight()
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
